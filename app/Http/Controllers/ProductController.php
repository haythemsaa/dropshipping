<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits avec filtres et pagination
     */
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'images', 'category'])
            ->where('status', 'active')
            ->whereNotNull('approved_at');

        // Filtrer par catégorie si spécifié
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtrer par prix
        if ($request->has('prix_min')) {
            $query->where('price', '>=', $request->prix_min);
        }
        if ($request->has('prix_max')) {
            $query->where('price', '<=', $request->prix_max);
        }

        // Tri
        $sort = $request->get('tri', 'recent');
        switch ($sort) {
            case 'prix_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'populaire':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(24);

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Affiche les détails d'un produit
     */
    public function show(Product $product)
    {
        // Vérifier que le produit est actif et approuvé
        if ($product->status !== 'active' || !$product->approved_at) {
            abort(404, 'Produit non disponible');
        }

        // Charger les relations
        $product->load(['supplier', 'images', 'category']);

        // Charger les avis approuvés
        $reviews = $product->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculer les stats d'avis
        $ratingDistribution = $product->getRatingDistribution();

        // Vérifier si l'utilisateur peut laisser un avis
        $canReview = auth()->check() &&
                     $product->canBeReviewedBy(auth()->user()) &&
                     !$product->hasReviewFrom(auth()->user());

        // Incrémenter le compteur de vues
        $product->increment('views_count');

        // Produits similaires de la même catégorie
        $relatedProducts = Product::with(['supplier', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->whereNotNull('approved_at')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'ratingDistribution', 'canReview'));
    }

    /**
     * Affiche les produits d'une catégorie
     */
    public function category(Category $category)
    {
        // Vérifier que la catégorie est active
        if (!$category->is_active) {
            abort(404, 'Catégorie non disponible');
        }

        // Récupérer les IDs de la catégorie et de ses sous-catégories
        $categoryIds = [$category->id];
        $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());

        $products = Product::with(['supplier', 'images', 'category'])
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->whereNotNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return view('products.category', compact('category', 'products'));
    }

    /**
     * Recherche de produits
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $products = Product::with(['supplier', 'images', 'category'])
            ->where('status', 'active')
            ->whereNotNull('approved_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('products.search', compact('products', 'query', 'categories'));
    }
}
