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
        'variant_id',
        'variant_attributes',
        'variant_sku',
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
        'variant_attributes' => 'array',
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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
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

    /**
     * Get display name with variant attributes
     */
    public function getDisplayName(): string
    {
        $name = $this->product_name;

        if ($this->variant_attributes) {
            $attributes = collect($this->variant_attributes)
                ->values()
                ->implode(' / ');
            $name .= ' - ' . $attributes;
        }

        return $name;
    }

    /**
     * Get formatted variant attributes
     */
    public function getFormattedVariantAttributes(bool $withLabels = false): string
    {
        if (empty($this->variant_attributes)) {
            return '';
        }

        if ($withLabels) {
            return collect($this->variant_attributes)
                ->map(fn($value, $key) => ucfirst($key) . ': ' . $value)
                ->implode(', ');
        }

        return collect($this->variant_attributes)->values()->implode(' / ');
    }

    /**
     * Get the SKU (variant SKU if available, otherwise product SKU)
     */
    public function getSku(): string
    {
        return $this->variant_sku ?? $this->product_sku;
    }
}
