<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'email',
        'name',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Generate unsubscribe token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->unsubscribe_token)) {
                $subscription->unsubscribe_token = Str::random(64);
            }
            if (empty($subscription->subscribed_at)) {
                $subscription->subscribed_at = now();
            }
        });
    }

    /**
     * Scope active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Unsubscribe
     */
    public function unsubscribe()
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Resubscribe
     */
    public function resubscribe()
    {
        $this->update([
            'is_active' => true,
            'unsubscribed_at' => null,
        ]);
    }
}
