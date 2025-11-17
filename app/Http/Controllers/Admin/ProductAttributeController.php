<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index()
    {
        $attributes = ProductAttribute::withCount('values')
            ->ordered()
            ->paginate(20);

        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_type' => 'required|in:select,color,button',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ProductAttribute::create($validated);

        return back()->with('success', 'Attribut créé avec succès.');
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_type' => 'required|in:select,color,button',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $attribute->update($validated);

        return back()->with('success', 'Attribut mis à jour avec succès.');
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(ProductAttribute $attribute)
    {
        try {
            $attribute->delete();
            return back()->with('success', 'Attribut supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet attribut.');
        }
    }

    /**
     * Display attribute values for a specific attribute.
     */
    public function values(ProductAttribute $attribute)
    {
        $values = $attribute->values()
            ->ordered()
            ->paginate(30);

        return view('admin.attributes.values', compact('attribute', 'values'));
    }

    /**
     * Store a new attribute value.
     */
    public function storeValue(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['attribute_id'] = $attribute->id;
        $validated['slug'] = Str::slug($validated['value']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ProductAttributeValue::create($validated);

        return back()->with('success', 'Valeur ajoutée avec succès.');
    }

    /**
     * Update an attribute value.
     */
    public function updateValue(Request $request, ProductAttribute $attribute, ProductAttributeValue $value)
    {
        if ($value->attribute_id !== $attribute->id) {
            abort(404);
        }

        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['value']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $value->update($validated);

        return back()->with('success', 'Valeur mise à jour avec succès.');
    }

    /**
     * Remove an attribute value.
     */
    public function destroyValue(ProductAttribute $attribute, ProductAttributeValue $value)
    {
        if ($value->attribute_id !== $attribute->id) {
            abort(404);
        }

        try {
            $value->delete();
            return back()->with('success', 'Valeur supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette valeur.');
        }
    }

    /**
     * Toggle attribute status.
     */
    public function toggleStatus(ProductAttribute $attribute)
    {
        $attribute->update(['is_active' => !$attribute->is_active]);
        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Toggle attribute value status.
     */
    public function toggleValueStatus(ProductAttribute $attribute, ProductAttributeValue $value)
    {
        if ($value->attribute_id !== $attribute->id) {
            abort(404);
        }

        $value->update(['is_active' => !$value->is_active]);
        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}
