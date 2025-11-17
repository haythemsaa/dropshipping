<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductComparisonService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    protected $comparisonService;

    public function __construct(ProductComparisonService $comparisonService)
    {
        $this->comparisonService = $comparisonService;
    }

    /**
     * Display comparison page
     */
    public function index()
    {
        $products = $this->comparisonService->getProducts();

        return view('comparison.index', compact('products'));
    }

    /**
     * Add product to comparison
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is active
        if ($product->status !== 'active' || !$product->approved_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'est pas disponible'
            ], 400);
        }

        $added = $this->comparisonService->addProduct($request->product_id);

        if (!$added) {
            if ($this->comparisonService->isFull()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous pouvez comparer maximum ' . $this->comparisonService->getMaxItems() . ' produits'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ce produit est déjà dans votre comparaison'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté à la comparaison',
            'count' => $this->comparisonService->getCount()
        ]);
    }

    /**
     * Remove product from comparison
     */
    public function remove(Product $product)
    {
        $this->comparisonService->removeProduct($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré de la comparaison',
            'count' => $this->comparisonService->getCount()
        ]);
    }

    /**
     * Clear all products from comparison
     */
    public function clear()
    {
        $this->comparisonService->clear();

        return redirect()->route('products.index')
            ->with('success', 'Comparaison vidée');
    }

    /**
     * Get comparison count
     */
    public function count()
    {
        return response()->json([
            'count' => $this->comparisonService->getCount()
        ]);
    }
}
