<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_item_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'helpful_count' => 'integer',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Le produit évalué
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * L'utilisateur qui a laissé l'avis
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * L'item de commande associé (pour vérifier l'achat)
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Scope pour les avis approuvés
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope pour les avis en attente de modération
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope pour les achats vérifiés
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Approuver l'avis
     */
    public function approve(): bool
    {
        return $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }

    /**
     * Rejeter l'avis
     */
    public function reject(): bool
    {
        return $this->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }

    /**
     * Incrémenter le compteur "utile"
     */
    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Vérifier si l'avis peut être modifié
     */
    public function canBeEdited(): bool
    {
        // Peut être modifié dans les 48h après création et seulement si pas encore approuvé
        return !$this->is_approved && $this->created_at->diffInHours(now()) < 48;
    }

    /**
     * Obtenir les étoiles sous forme de texte
     */
    public function getStarsTextAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Obtenir un extrait du commentaire
     */
    public function getExcerptAttribute(): ?string
    {
        if (!$this->comment) {
            return null;
        }

        return strlen($this->comment) > 150
            ? substr($this->comment, 0, 150) . '...'
            : $this->comment;
    }

    /**
     * Vérifier si c'est un achat vérifié
     */
    public function isVerifiedPurchase(): bool
    {
        return $this->is_verified_purchase;
    }

    /**
     * Vérifier si l'avis est approuvé
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }
}
