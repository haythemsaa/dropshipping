<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Affiche le panier
     */
    public function index()
    {
        $cart = $this->getOrCreateCart();

        if (!$cart) {
            return view('cart.index', ['cart' => null, 'items' => collect([])]);
        }

        $cart->load(['items.product.images', 'items.product.supplier']);

        return view('cart.index', ['cart' => $cart, 'items' => $cart->items]);
    }

    /**
     * Ajoute un produit au panier
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Vérifier que le produit est disponible
        if ($product->status !== 'active' || !$product->approved_at) {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        // Vérifier le stock
        if (!$product->isInStock() || $product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Stock insuffisant pour ce produit.');
        }

        DB::beginTransaction();
        try {
            $cart = $this->getOrCreateCart();

            // Vérifier si le produit est déjà dans le panier
            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                // Mettre à jour la quantité
                $newQuantity = $cartItem->quantity + $request->quantity;

                if ($product->stock_quantity < $newQuantity) {
                    DB::rollBack();
                    return back()->with('error', 'Stock insuffisant pour cette quantité.');
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Créer un nouvel item
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Produit ajouté au panier avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout au panier.');
        }
    }

    /**
     * Met à jour la quantité d'un item du panier
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Vérifier que l'item appartient au panier de l'utilisateur
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $product = $cartItem->product;

        // Vérifier le stock
        if ($product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Stock insuffisant pour cette quantité.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Panier mis à jour avec succès.');
    }

    /**
     * Supprime un item du panier
     */
    public function remove(CartItem $cartItem)
    {
        // Vérifier que l'item appartient au panier de l'utilisateur
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produit retiré du panier.');
    }

    /**
     * Vide le panier
     */
    public function clear()
    {
        $cart = $this->getOrCreateCart();

        if ($cart) {
            $cart->clear();
        }

        return back()->with('success', 'Panier vidé avec succès.');
    }

    /**
     * Récupère ou crée le panier pour l'utilisateur courant
     */
    private function getOrCreateCart()
    {
        if (auth()->check()) {
            // Utilisateur connecté
            $cart = Cart::firstOrCreate(
                ['user_id' => auth()->id()],
                ['session_id' => null]
            );

            // Fusionner le panier de session si existe
            $this->mergeSessionCart($cart);

            return $cart;
        } else {
            // Utilisateur invité - utiliser le session_id
            $sessionId = session()->getId();

            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null]
            );
        }
    }

    /**
     * Fusionne le panier de session avec le panier utilisateur lors de la connexion
     */
    private function mergeSessionCart(Cart $userCart)
    {
        $sessionId = session()->getId();
        $sessionCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if ($sessionCart) {
            // Fusionner les items
            foreach ($sessionCart->items as $sessionItem) {
                $existingItem = $userCart->items()
                    ->where('product_id', $sessionItem->product_id)
                    ->first();

                if ($existingItem) {
                    // Additionner les quantités
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $sessionItem->quantity
                    ]);
                } else {
                    // Transférer l'item
                    $sessionItem->update(['cart_id' => $userCart->id]);
                }
            }

            // Supprimer le panier de session
            $sessionCart->delete();
        }
    }
}
