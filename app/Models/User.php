<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'status',
        'business_name', 'business_address', 'business_phone', 'business_email',
        'business_sector', 'business_document', 'logo',
        'bank_name', 'bank_account_number', 'bank_rib',
        'sms_notifications', 'email_notifications', 'commission_rate',
        'approved_at', 'approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sms_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'commission_rate' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    // Role checking methods
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    // Relationships for suppliers
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'supplier_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'supplier_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'supplier_id');
    }

    // Relationships for clients
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // Relationship for approvals
    public function approvedSuppliers(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'approved_by');
    }

    // Helper methods
    public function getTotalSales(): float
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->sum('subtotal');
    }

    public function getTotalCommissionsEarned(): float
    {
        return $this->commissions()
            ->where('status', 'approved')
            ->sum('commission_amount');
    }

    public function getPendingCommissions(): float
    {
        return $this->commissions()
            ->where('payment_status', 'unpaid')
            ->sum('supplier_amount');
    }

    public function hasInWishlist(int $productId): bool
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }
}
