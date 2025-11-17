<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's wishlist.
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()
            ->with(['product' => function ($query) {
                $query->with(['supplier', 'images'])
                    ->where('status', 'active')
                    ->whereNotNull('approved_at');
            }])
            ->latest()
            ->paginate(24);

        return view('wishlists.index', compact('wishlists'));
    }

    /**
     * Toggle product in wishlist (add or remove).
     */
    public function toggle(Request $request, Product $product)
    {
        $user = Auth::user();

        $wishlistItem = $user->wishlists()->where('product_id', $product->id)->first();

        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Produit retiré de vos favoris',
                'wishlistCount' => $user->wishlists()->count(),
            ]);
        } else {
            // Add to wishlist
            $user->wishlists()->create([
                'product_id' => $product->id,
            ]);

            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Produit ajouté à vos favoris',
                'wishlistCount' => $user->wishlists()->count(),
            ]);
        }
    }

    /**
     * Remove a product from wishlist.
     */
    public function destroy(Wishlist $wishlist)
    {
        // Ensure the wishlist belongs to the authenticated user
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();

        return redirect()->route('wishlist.index')
            ->with('success', 'Produit retiré de vos favoris');
    }
}
