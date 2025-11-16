<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->with('parent')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        // Grouper par catégories parentes
        $parentCategories = $categories->whereNull('parent_id');

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');
            $validated['order'] = $validated['order'] ?? 0;

            Category::create($validated);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la catégorie.');
        }
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Empêcher une catégorie de devenir sa propre parente
        if ($validated['parent_id'] == $category->id) {
            return back()->with('error', 'Une catégorie ne peut pas être sa propre parente.');
        }

        DB::beginTransaction();
        try {
            $validated['is_active'] = $request->has('is_active');

            // Régénérer le slug si le nom a changé
            if ($validated['name'] !== $category->name) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $category->update($validated);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier qu'il n'y a pas de produits dans cette catégorie
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des produits.');
        }

        // Vérifier qu'il n'y a pas de sous-catégories
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie ayant des sous-catégories.');
        }

        DB::beginTransaction();
        try {
            $category->delete();

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Active/désactive une catégorie
     */
    public function toggleStatus(Category $category)
    {
        DB::beginTransaction();
        try {
            $category->update([
                'is_active' => !$category->is_active
            ]);

            // Si désactivée, désactiver aussi les sous-catégories
            if (!$category->is_active) {
                $category->children()->update(['is_active' => false]);
            }

            DB::commit();

            $status = $category->is_active ? 'activée' : 'désactivée';
            return back()->with('success', "Catégorie {$status} avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }
}
