<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id', 'category_id', 'name', 'slug', 'description', 'specifications',
        'price', 'cost_price', 'stock_quantity', 'low_stock_threshold', 'sku', 'brand',
        'weight', 'dimensions', 'status', 'is_featured', 'views_count', 'sales_count',
        'video_url', 'tags', 'shipping_days', 'shipping_cost', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'shipping_days' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Calculer la note moyenne du produit
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Compter les avis approuvés
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Obtenir la distribution des notes (1-5 étoiles)
     */
    public function getRatingDistribution(): array
    {
        $distribution = [];
        $total = $this->reviewsCount;

        for ($i = 5; $i >= 1; $i--) {
            $count = $this->approvedReviews()->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return $distribution;
    }

    /**
     * Vérifier si un utilisateur peut laisser un avis
     */
    public function canBeReviewedBy(User $user): bool
    {
        // Vérifier que l'utilisateur a acheté et reçu ce produit
        return $this->orderItems()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'delivered');
            })
            ->exists();
    }

    /**
     * Vérifier si un utilisateur a déjà laissé un avis
     */
    public function hasReviewFrom(User $user): bool
    {
        return $this->reviews()->where('user_id', $user->id)->exists();
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold && $this->stock_quantity > 0;
    }

    public function decrementStock(int $quantity): void
    {
        $this->decrement('stock_quantity', $quantity);
        if ($this->stock_quantity <= 0) {
            $this->update(['status' => 'out_of_stock']);
        }
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
        if ($this->stock_quantity > 0 && $this->status === 'out_of_stock') {
            $this->update(['status' => 'active']);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}
