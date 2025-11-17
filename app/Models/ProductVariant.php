<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'stock_quantity',
        'attributes',
        'attribute_value_ids',
        'image_path',
        'is_default',
        'is_active',
        'position',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'attributes' => 'array',
        'attribute_value_ids' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Get the product that owns this variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include in-stock variants.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('created_at');
    }

    /**
     * Scope to get default variant.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if variant is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if variant is available for purchase.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->isInStock();
    }

    /**
     * Get the discount percentage if compare_at_price is set.
     */
    public function getDiscountPercentage(): ?int
    {
        if (!$this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }

        return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    /**
     * Check if variant has a discount.
     */
    public function hasDiscount(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    /**
     * Get variant image URL.
     */
    public function getImageUrl(): ?string
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }

        // Fallback to product's first image
        return $this->product->images->first()?->image_url;
    }

    /**
     * Get formatted attributes for display.
     * Example: "Rouge / M" or "Couleur: Rouge, Taille: M"
     */
    public function getFormattedAttributes(bool $withLabels = false): string
    {
        if (empty($this->attributes)) {
            return '';
        }

        if ($withLabels) {
            return collect($this->attributes)
                ->map(fn($value, $key) => ucfirst($key) . ': ' . $value)
                ->implode(', ');
        }

        return collect($this->attributes)->implode(' / ');
    }

    /**
     * Get full variant name with attributes.
     * Example: "T-shirt Premium - Rouge / M"
     */
    public function getFullName(): string
    {
        $name = $this->product->name;
        $attrs = $this->getFormattedAttributes();

        return $attrs ? "{$name} - {$attrs}" : $name;
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    /**
     * Set as default variant for the product.
     */
    public function setAsDefault(): void
    {
        // Unset all other default variants for this product
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Get attribute value models.
     */
    public function getAttributeValues()
    {
        if (empty($this->attribute_value_ids)) {
            return collect();
        }

        return ProductAttributeValue::whereIn('id', $this->attribute_value_ids)
            ->with('attribute')
            ->get();
    }
}
