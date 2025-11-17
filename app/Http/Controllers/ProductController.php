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

        // Filtrer par catégorie(s) - support multi-sélection
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryIds = is_array($request->categories) ? $request->categories : [$request->categories];

            // Inclure les sous-catégories
            $allCategoryIds = [];
            foreach ($categoryIds as $catId) {
                $allCategoryIds[] = $catId;
                $subCategories = Category::where('parent_id', $catId)->pluck('id')->toArray();
                $allCategoryIds = array_merge($allCategoryIds, $subCategories);
            }

            $query->whereIn('category_id', $allCategoryIds);
        }

        // Filtrer par prix
        if ($request->filled('prix_min')) {
            $query->where('price', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('price', '<=', $request->prix_max);
        }

        // Filtrer par stock disponible
        if ($request->has('en_stock') && $request->en_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Filtrer par note moyenne
        if ($request->filled('note_min')) {
            $query->withAvg('approvedReviews as avg_rating', 'rating')
                  ->having('avg_rating', '>=', $request->note_min);
        }

        // Recherche par mot-clé
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($q2) use ($searchTerm) {
                      $q2->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
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
            case 'meilleures_notes':
                $query->withAvg('approvedReviews as avg_rating', 'rating')
                      ->orderBy('avg_rating', 'desc');
                break;
            case 'nom_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(24)->withQueryString();

        // Récupérer toutes les catégories pour les filtres
        $categories = Category::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        // Statistiques pour l'affichage
        $stats = [
            'total' => $products->total(),
            'prix_min' => Product::where('status', 'active')->whereNotNull('approved_at')->min('price') ?? 0,
            'prix_max' => Product::where('status', 'active')->whereNotNull('approved_at')->max('price') ?? 1000,
        ];

        return view('products.index', compact('products', 'categories', 'stats'));
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
        $searchQuery = $request->get('q', '');

        if (empty($searchQuery)) {
            return redirect()->route('products.index');
        }

        // Utiliser la même logique de filtrage que index()
        return $this->index($request);
    }

    /**
     * API pour l'autocomplete de recherche
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 'active')
            ->whereNotNull('approved_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'slug', 'price')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => number_format($product->price, 2),
                    'url' => route('products.show', $product->slug),
                ];
            });

        return response()->json($products);
    }
}
