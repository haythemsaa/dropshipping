<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'category_ids',
        'product_ids',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'category_ids' => 'array',
        'product_ids' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the usage records for this coupon.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon.
     */
    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $userUsageCount = $this->usages()->where('user_id', $user->id)->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    /**
     * Calculate discount amount for given cart.
     */
    public function calculateDiscount(float $subtotal, array $cartItems = []): float
    {
        // Check minimum order amount
        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return 0;
        }

        // Check if coupon applies to specific categories or products
        if ($this->category_ids || $this->product_ids) {
            $applicableAmount = $this->getApplicableAmount($cartItems);
            if ($applicableAmount == 0) {
                return 0;
            }
            $subtotal = $applicableAmount;
        }

        $discount = 0;

        switch ($this->type) {
            case 'percentage':
                $discount = ($subtotal * $this->value) / 100;
                if ($this->max_discount_amount) {
                    $discount = min($discount, $this->max_discount_amount);
                }
                break;

            case 'fixed':
                $discount = min($this->value, $subtotal);
                break;

            case 'free_shipping':
                // Handled separately in checkout
                $discount = 0;
                break;
        }

        return round($discount, 2);
    }

    /**
     * Get applicable amount for category/product specific coupons.
     */
    private function getApplicableAmount(array $cartItems): float
    {
        $applicableAmount = 0;

        foreach ($cartItems as $item) {
            $product = $item['product'] ?? null;
            if (!$product) continue;

            $applies = false;

            // Check if product is in allowed products
            if ($this->product_ids && in_array($product->id, $this->product_ids)) {
                $applies = true;
            }

            // Check if product's category is in allowed categories
            if ($this->category_ids && in_array($product->category_id, $this->category_ids)) {
                $applies = true;
            }

            if ($applies) {
                $applicableAmount += $product->price * $item['quantity'];
            }
        }

        return $applicableAmount;
    }

    /**
     * Increment times used counter.
     */
    public function incrementUsage(): void
    {
        $this->increment('times_used');
    }

    /**
     * Record usage by a user for an order.
     */
    public function recordUsage(User $user, int $orderId, float $discountAmount): void
    {
        $this->usages()->create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);

        $this->incrementUsage();
    }

    /**
     * Scope for active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    /**
     * Get validation error message.
     */
    public function getValidationError(User $user = null, float $subtotal = 0): ?string
    {
        if (!$this->is_active) {
            return 'Ce coupon n\'est plus actif.';
        }

        $now = Carbon::now();
        if ($now->lt($this->valid_from)) {
            return 'Ce coupon n\'est pas encore valide.';
        }

        if ($now->gt($this->valid_until)) {
            return 'Ce coupon a expiré le ' . $this->valid_until->format('d/m/Y');
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return 'Ce coupon a atteint sa limite d\'utilisation.';
        }

        if ($user) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return 'Vous avez déjà utilisé ce coupon le nombre maximum de fois.';
            }
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return 'Montant minimum de commande: ' . number_format($this->min_order_amount, 2) . ' TND';
        }

        return null;
    }
}
