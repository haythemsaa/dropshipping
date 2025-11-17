<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    /**
     * Display the variant management page for a product.
     */
    public function index(Product $product)
    {
        // Ensure the product belongs to the authenticated supplier
        if ($product->supplier_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer ce produit.');
        }

        $variants = $product->variants()
            ->orderBy('position')
            ->orderBy('created_at')
            ->get();

        $attributes = ProductAttribute::active()
            ->with('activeValues')
            ->ordered()
            ->get();

        return view('supplier.products.variants.index', compact('product', 'variants', 'attributes'));
    }

    /**
     * Show the form for creating a new variant.
     */
    public function create(Product $product)
    {
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $attributes = ProductAttribute::active()
            ->with('activeValues')
            ->ordered()
            ->get();

        return view('supplier.products.variants.create', compact('product', 'attributes'));
    }

    /**
     * Store a newly created variant.
     */
    public function store(Request $request, Product $product)
    {
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0|gte:price',
            'stock_quantity' => 'required|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
            'image' => 'nullable|image|max:2048',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Build attributes array from selected values
            $attributeValues = \App\Models\ProductAttributeValue::with('attribute')
                ->whereIn('id', $validated['attribute_values'])
                ->get();

            $attributes = [];
            foreach ($attributeValues as $value) {
                $attributes[$value->attribute->slug] = $value->value;
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products/variants', 'public');
            }

            // Create variant
            $variant = $product->variants()->create([
                'sku' => $validated['sku'],
                'price' => $validated['price'],
                'compare_at_price' => $validated['compare_at_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'attributes' => $attributes,
                'attribute_value_ids' => $validated['attribute_values'],
                'image_path' => $imagePath,
                'is_default' => $request->boolean('is_default'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Set as default if requested
            if ($request->boolean('is_default')) {
                $variant->setAsDefault();
            }

            DB::commit();

            return redirect()
                ->route('supplier.products.variants.index', $product)
                ->with('success', 'Variante créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return back()->withInput()->with('error', 'Erreur lors de la création de la variante: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a variant.
     */
    public function edit(Product $product, ProductVariant $variant)
    {
        if ($product->supplier_id !== auth()->id() || $variant->product_id !== $product->id) {
            abort(403);
        }

        $attributes = ProductAttribute::active()
            ->with('activeValues')
            ->ordered()
            ->get();

        return view('supplier.products.variants.edit', compact('product', 'variant', 'attributes'));
    }

    /**
     * Update the specified variant.
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        if ($product->supplier_id !== auth()->id() || $variant->product_id !== $product->id) {
            abort(403);
        }

        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0|gte:price',
            'stock_quantity' => 'required|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:product_attribute_values,id',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Build attributes array
            $attributeValues = \App\Models\ProductAttributeValue::with('attribute')
                ->whereIn('id', $validated['attribute_values'])
                ->get();

            $attributes = [];
            foreach ($attributeValues as $value) {
                $attributes[$value->attribute->slug] = $value->value;
            }

            // Handle image
            $imagePath = $variant->image_path;
            if ($request->boolean('remove_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            } elseif ($request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('products/variants', 'public');
            }

            // Update variant
            $variant->update([
                'sku' => $validated['sku'],
                'price' => $validated['price'],
                'compare_at_price' => $validated['compare_at_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'attributes' => $attributes,
                'attribute_value_ids' => $validated['attribute_values'],
                'image_path' => $imagePath,
                'is_default' => $request->boolean('is_default'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->boolean('is_default')) {
                $variant->setAsDefault();
            }

            DB::commit();

            return redirect()
                ->route('supplier.products.variants.index', $product)
                ->with('success', 'Variante mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
        if ($product->supplier_id !== auth()->id() || $variant->product_id !== $product->id) {
            abort(403);
        }

        try {
            // Delete image if exists
            if ($variant->image_path) {
                Storage::disk('public')->delete($variant->image_path);
            }

            $variant->delete();

            return redirect()
                ->route('supplier.products.variants.index', $product)
                ->with('success', 'Variante supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Generate multiple variants from attribute combinations.
     */
    public function generateBulk(Request $request, Product $product)
    {
        if ($product->supplier_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'attributes' => 'required|array|min:1',
            'attributes.*' => 'array',
            'attributes.*.*' => 'exists:product_attribute_values,id',
            'base_price' => 'required|numeric|min:0',
            'base_stock' => 'required|integer|min:0',
            'sku_prefix' => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Generate all combinations
            $combinations = $this->generateCombinations($validated['attributes']);
            $created = 0;

            foreach ($combinations as $combination) {
                // Build attributes array
                $attributeValues = \App\Models\ProductAttributeValue::with('attribute')
                    ->whereIn('id', $combination)
                    ->get();

                $attributes = [];
                $skuParts = [];
                foreach ($attributeValues as $value) {
                    $attributes[$value->attribute->slug] = $value->value;
                    $skuParts[] = strtoupper(substr($value->slug, 0, 3));
                }

                $sku = $validated['sku_prefix'] . '-' . implode('-', $skuParts);

                // Check if variant with this combination already exists
                $exists = $product->variants()
                    ->where('attribute_value_ids', json_encode($combination))
                    ->exists();

                if (!$exists) {
                    $product->variants()->create([
                        'sku' => $sku,
                        'price' => $validated['base_price'],
                        'stock_quantity' => $validated['base_stock'],
                        'attributes' => $attributes,
                        'attribute_value_ids' => $combination,
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return redirect()
                ->route('supplier.products.variants.index', $product)
                ->with('success', "{$created} variantes créées avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
        }
    }

    /**
     * Generate all combinations of attribute values.
     */
    private function generateCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $key => $array) {
            $tmp = [];
            foreach ($result as $resultItem) {
                foreach ($array as $item) {
                    $tmp[] = array_merge($resultItem, [$item]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    /**
     * Toggle variant status.
     */
    public function toggleStatus(Product $product, ProductVariant $variant)
    {
        if ($product->supplier_id !== auth()->id() || $variant->product_id !== $product->id) {
            abort(403);
        }

        $variant->update(['is_active' => !$variant->is_active]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}
