<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Imports\ProductsImport;
use App\Exports\ProductsTemplateExport;
use App\Exports\SupplierProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits du fournisseur
     */
    public function index(Request $request)
    {
        $supplier = auth()->user();

        $query = $supplier->products()->with(['category', 'images']);

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

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('supplier.products.index', compact('products'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->whereNotNull('parent_id') // Seulement les sous-catégories
            ->orderBy('name')
            ->get();

        return view('supplier.products.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau produit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['supplier_id'] = auth()->id();
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
            $validated['status'] = 'draft';
            $validated['approved_at'] = null; // Nécessite approbation admin
            $validated['is_featured'] = $request->has('is_featured');

            $product = Product::create($validated);

            DB::commit();

            return redirect()->route('supplier.products.edit', $product)
                ->with('success', 'Produit créé avec succès. N\'oubliez pas d\'ajouter des images.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du produit.');
        }
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Product $product)
    {
        // Vérifier que le produit appartient au fournisseur
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $product->load('images');

        $categories = Category::where('is_active', true)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('supplier.products.edit', compact('product', 'categories'));
    }

    /**
     * Met à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        // Vérifier que le produit appartient au fournisseur
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        // Régénérer le slug si le nom a changé
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        }

        // Si modifié après approbation, remettre en attente
        if ($product->approved_at && $product->wasChanged(['name', 'price', 'description'])) {
            $validated['approved_at'] = null;
        }

        $product->update($validated);

        return back()->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit
     */
    public function destroy(Product $product)
    {
        // Vérifier que le produit appartient au fournisseur
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier qu'il n'y a pas de commandes en cours
        if ($product->orderItems()->whereIn('status', ['pending', 'processing'])->exists()) {
            return back()->with('error', 'Impossible de supprimer un produit avec des commandes en cours.');
        }

        DB::beginTransaction();
        try {
            // Supprimer les images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Soft delete du produit
            $product->delete();

            DB::commit();

            return redirect()->route('supplier.products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Active/désactive un produit
     */
    public function toggleStatus(Product $product)
    {
        // Vérifier que le produit appartient au fournisseur
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que le produit est approuvé
        if (!$product->approved_at) {
            return back()->with('error', 'Le produit doit être approuvé avant d\'être activé.');
        }

        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return back()->with('success', 'Statut du produit mis à jour.');
    }

    /**
     * Upload une image pour le produit
     */
    public function uploadImage(Request $request, Product $product)
    {
        // Vérifier que le produit appartient au fournisseur
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Upload l'image
            $path = $request->file('image')->store('products', 'public');

            // Déterminer si c'est la première image (sera primaire)
            $isPrimary = $product->images()->count() === 0;

            // Si c'est la première, mettre toutes les autres en non-primaire
            if (!$isPrimary && $request->has('is_primary')) {
                $product->images()->update(['is_primary' => false]);
                $isPrimary = true;
            }

            // Créer l'enregistrement
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'order' => $product->images()->max('order') + 1,
            ]);

            DB::commit();

            return back()->with('success', 'Image ajoutée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
        }
    }

    /**
     * Supprime une image
     */
    public function deleteImage(ProductImage $image)
    {
        // Vérifier que l'image appartient à un produit du fournisseur
        if ($image->product->supplier_id !== auth()->id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Supprimer le fichier
            Storage::disk('public')->delete($image->image_path);

            $wasPrimary = $image->is_primary;
            $product = $image->product;

            // Supprimer l'enregistrement
            $image->delete();

            // Si c'était l'image primaire, définir la suivante comme primaire
            if ($wasPrimary) {
                $nextImage = $product->images()->orderBy('order')->first();
                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return back()->with('success', 'Image supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'image.');
        }
    }

    /**
     * Définit une image comme primaire
     */
    public function setPrimaryImage(ProductImage $image)
    {
        // Vérifier que l'image appartient à un produit du fournisseur
        if ($image->product->supplier_id !== auth()->id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Mettre toutes les images en non-primaire
            $image->product->images()->update(['is_primary' => false]);

            // Définir celle-ci comme primaire
            $image->update(['is_primary' => true]);

            DB::commit();

            return back()->with('success', 'Image principale mise à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Affiche la page d'import de produits
     */
    public function showImport()
    {
        $categories = Category::where('is_active', true)->get();
        return view('supplier.products.import', compact('categories'));
    }

    /**
     * Télécharge le template CSV
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new ProductsTemplateExport(),
            'template_produits_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Import de produits en masse (CSV/Excel)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            $import = new ProductsImport(auth()->id());

            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();
            $failures = $import->failures();

            // Préparer le message de succès
            $message = "Import terminé ! ";
            $message .= "{$stats['imported']} produits importés avec succès.";

            if ($stats['skipped'] > 0) {
                $message .= " {$stats['skipped']} lignes ignorées (vides).";
            }

            if ($stats['failed'] > 0) {
                $message .= " {$stats['failed']} produits ont échoué.";

                // Stocker les erreurs dans la session pour les afficher
                $errors = [];
                foreach ($failures as $failure) {
                    $errors[] = [
                        'row' => $failure->row(),
                        'errors' => $failure->errors(),
                    ];
                }

                session()->flash('import_errors', $errors);

                return redirect()->route('supplier.products.import')
                    ->with('warning', $message);
            }

            return redirect()->route('supplier.products.index')
                ->with('success', $message . ' Les produits sont en attente d\'approbation.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Export des produits en Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'category_id']);

        return Excel::download(
            new SupplierProductsExport(auth()->id(), $filters),
            'produits_' . date('Y-m-d') . '.xlsx'
        );
    }
}
