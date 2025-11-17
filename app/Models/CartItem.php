<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getSubtotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get display name (product name + variant attributes if applicable).
     */
    public function getDisplayName(): string
    {
        if ($this->variant_id && $this->variant) {
            return $this->product->name . ' - ' . $this->variant->getFormattedAttributes();
        }

        return $this->product->name;
    }
}
