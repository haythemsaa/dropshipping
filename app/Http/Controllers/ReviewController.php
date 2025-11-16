<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Affiche le formulaire de création d'avis
     */
    public function create(Product $product)
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur peut laisser un avis
        if (!$product->canBeReviewedBy($user)) {
            return back()->with('error', 'Vous devez avoir acheté et reçu ce produit pour laisser un avis.');
        }

        // Vérifier qu'il n'a pas déjà laissé d'avis
        if ($product->hasReviewFrom($user)) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        // Récupérer l'OrderItem correspondant pour vérifier l'achat
        $orderItem = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'delivered');
            })
            ->latest()
            ->first();

        return view('reviews.create', compact('product', 'orderItem'));
    }

    /**
     * Enregistre un nouvel avis
     */
    public function store(Request $request, Product $product)
    {
        $user = auth()->user();

        // Vérifications
        if (!$product->canBeReviewedBy($user)) {
            return back()->with('error', 'Vous devez avoir acheté et reçu ce produit pour laisser un avis.');
        }

        if ($product->hasReviewFrom($user)) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:2000',
            'order_item_id' => 'required|exists:order_items,id',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier que l'order_item appartient bien à l'utilisateur
            $orderItem = OrderItem::where('id', $validated['order_item_id'])
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('status', 'delivered');
                })
                ->firstOrFail();

            ProductReview::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'order_item_id' => $orderItem->id,
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'is_verified_purchase' => true,
                'is_approved' => false, // Nécessite modération
            ]);

            // TODO: Notifier le fournisseur et l'admin

            DB::commit();

            return redirect()->route('products.show', $product)
                ->with('success', 'Merci pour votre avis ! Il sera publié après modération.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre avis.');
        }
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(ProductReview $review)
    {
        // Vérifier que l'avis appartient à l'utilisateur
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que l'avis peut être modifié
        if (!$review->canBeEdited()) {
            return back()->with('error', 'Cet avis ne peut plus être modifié.');
        }

        $product = $review->product;
        return view('reviews.edit', compact('review', 'product'));
    }

    /**
     * Met à jour un avis
     */
    public function update(Request $request, ProductReview $review)
    {
        // Vérifier que l'avis appartient à l'utilisateur
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que l'avis peut être modifié
        if (!$review->canBeEdited()) {
            return back()->with('error', 'Cet avis ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review->update($validated);

        return redirect()->route('products.show', $review->product)
            ->with('success', 'Votre avis a été mis à jour.');
    }

    /**
     * Supprime un avis
     */
    public function destroy(ProductReview $review)
    {
        // Vérifier que l'avis appartient à l'utilisateur
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que l'avis peut être supprimé
        if (!$review->canBeEdited()) {
            return back()->with('error', 'Cet avis ne peut plus être supprimé.');
        }

        $product = $review->product;
        $review->delete();

        return redirect()->route('products.show', $product)
            ->with('success', 'Votre avis a été supprimé.');
    }

    /**
     * Marquer un avis comme utile
     */
    public function markHelpful(ProductReview $review)
    {
        if (!$review->is_approved) {
            abort(404);
        }

        $review->incrementHelpful();

        return back()->with('success', 'Merci pour votre retour !');
    }
}
