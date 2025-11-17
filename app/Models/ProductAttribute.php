<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_type',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get the attribute values for this attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id')
            ->orderBy('position');
    }

    /**
     * Get active attribute values.
     */
    public function activeValues(): HasMany
    {
        return $this->values()->where('is_active', true);
    }

    /**
     * Scope a query to only include active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }

    /**
     * Check if this is a color attribute.
     */
    public function isColorAttribute(): bool
    {
        return $this->display_type === 'color';
    }

    /**
     * Check if this is a button attribute.
     */
    public function isButtonAttribute(): bool
    {
        return $this->display_type === 'button';
    }
}
