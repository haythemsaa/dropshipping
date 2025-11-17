<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'user_id',
        'email',
        'is_notified',
        'notified_at',
    ];

    protected $casts = [
        'is_notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending alerts
     */
    public function scopePending($query)
    {
        return $query->where('is_notified', false);
    }

    /**
     * Scope for notified alerts
     */
    public function scopeNotified($query)
    {
        return $query->where('is_notified', true);
    }

    /**
     * Mark alert as notified
     */
    public function markAsNotified(): void
    {
        $this->update([
            'is_notified' => true,
            'notified_at' => now(),
        ]);
    }
}
