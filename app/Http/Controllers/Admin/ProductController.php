<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits
     */
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'category']);

        // Filtrer par statut d'approbation
        if ($request->has('approval') && $request->approval !== 'all') {
            if ($request->approval === 'pending') {
                $query->whereNull('approved_at');
            } else {
                $query->whereNotNull('approved_at');
            }
        }

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Compter par statut
        $statusCounts = [
            'all' => Product::count(),
            'pending' => Product::whereNull('approved_at')->count(),
            'approved' => Product::whereNotNull('approved_at')->count(),
        ];

        return view('admin.products.index', compact('products', 'statusCounts'));
    }

    /**
     * Affiche les détails d'un produit
     */
    public function show(Product $product)
    {
        $product->load(['supplier', 'category', 'images', 'orderItems.order']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Approuve un produit
     */
    public function approve(Product $product)
    {
        if ($product->approved_at) {
            return back()->with('error', 'Ce produit est déjà approuvé.');
        }

        DB::beginTransaction();
        try {
            $product->update([
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'status' => 'active',
            ]);

            // TODO: Notifier le fournisseur

            DB::commit();

            return back()->with('success', 'Produit approuvé et activé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'approbation.');
        }
    }

    /**
     * Rejette un produit
     */
    public function reject(Product $product)
    {
        if ($product->approved_at) {
            return back()->with('error', 'Impossible de rejeter un produit déjà approuvé.');
        }

        DB::beginTransaction();
        try {
            $product->update([
                'status' => 'inactive',
            ]);

            // TODO: Notifier le fournisseur avec raison du rejet

            DB::commit();

            return back()->with('success', 'Produit rejeté.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du rejet.');
        }
    }

    /**
     * Supprime un produit
     */
    public function destroy(Product $product)
    {
        // Vérifier qu'il n'y a pas de commandes en cours
        if ($product->orderItems()->whereIn('status', ['pending', 'processing', 'shipped'])->exists()) {
            return back()->with('error', 'Impossible de supprimer un produit avec des commandes en cours.');
        }

        DB::beginTransaction();
        try {
            // Supprimer les images
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}
