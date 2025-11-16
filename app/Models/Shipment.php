<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_item_id', 'supplier_id', 'tracking_number', 'carrier',
        'status', 'shipped_at', 'delivered_at', 'estimated_delivery',
        'shipping_notes', 'tracking_history',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function updateTrackingHistory(array $update): void
    {
        $history = json_decode($this->tracking_history ?? '[]', true);
        $history[] = array_merge($update, ['timestamp' => now()->toIso8601String()]);
        $this->tracking_history = json_encode($history);
        $this->save();
    }
}
