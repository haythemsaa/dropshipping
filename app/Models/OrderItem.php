<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'supplier_id',
        'product_name',
        'product_sku',
        'price',
        'quantity',
        'subtotal',
        'status',
        'supplier_amount',
        'commission_amount',
        'commission_rate',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'supplier_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    /**
     * Calculate commission for this item
     */
    public function calculateCommission(): void
    {
        $commissionRate = $this->commission_rate ?? $this->supplier->commission_rate ?? 10;
        $this->commission_rate = $commissionRate;
        $this->commission_amount = ($this->subtotal * $commissionRate) / 100;
        $this->supplier_amount = $this->subtotal - $this->commission_amount;
        $this->save();
    }
}
