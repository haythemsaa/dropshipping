<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les attributs de base
        $colorAttribute = ProductAttribute::create([
            'name' => 'Couleur',
            'slug' => 'couleur',
            'display_type' => 'color',
            'position' => 1,
            'is_active' => true,
        ]);

        $sizeAttribute = ProductAttribute::create([
            'name' => 'Taille',
            'slug' => 'taille',
            'display_type' => 'button',
            'position' => 2,
            'is_active' => true,
        ]);

        $materialAttribute = ProductAttribute::create([
            'name' => 'Matière',
            'slug' => 'matiere',
            'display_type' => 'select',
            'position' => 3,
            'is_active' => true,
        ]);

        // Valeurs pour Couleur
        $colors = [
            ['value' => 'Rouge', 'slug' => 'rouge', 'color_code' => '#EF4444', 'position' => 1],
            ['value' => 'Bleu', 'slug' => 'bleu', 'color_code' => '#3B82F6', 'position' => 2],
            ['value' => 'Vert', 'slug' => 'vert', 'color_code' => '#10B981', 'position' => 3],
            ['value' => 'Noir', 'slug' => 'noir', 'color_code' => '#000000', 'position' => 4],
            ['value' => 'Blanc', 'slug' => 'blanc', 'color_code' => '#FFFFFF', 'position' => 5],
        ];

        foreach ($colors as $color) {
            $colorAttribute->values()->create($color);
        }

        // Valeurs pour Taille
        $sizes = [
            ['value' => 'S', 'slug' => 's', 'position' => 1],
            ['value' => 'M', 'slug' => 'm', 'position' => 2],
            ['value' => 'L', 'slug' => 'l', 'position' => 3],
            ['value' => 'XL', 'slug' => 'xl', 'position' => 4],
        ];

        foreach ($sizes as $size) {
            $sizeAttribute->values()->create($size);
        }

        // Valeurs pour Matière
        $materials = [
            ['value' => 'Coton', 'slug' => 'coton', 'position' => 1],
            ['value' => 'Polyester', 'slug' => 'polyester', 'position' => 2],
            ['value' => 'Laine', 'slug' => 'laine', 'position' => 3],
        ];

        foreach ($materials as $material) {
            $materialAttribute->values()->create($material);
        }

        // Créer des variantes pour les produits existants
        $this->createVariantsForProducts();
    }

    /**
     * Créer des variantes pour quelques produits de démonstration
     */
    private function createVariantsForProducts(): void
    {
        // Récupérer les attributs et valeurs
        $colorValues = ProductAttributeValue::where('attribute_id',
            ProductAttribute::where('slug', 'couleur')->first()->id
        )->get();

        $sizeValues = ProductAttributeValue::where('attribute_id',
            ProductAttribute::where('slug', 'taille')->first()->id
        )->get();

        // Trouver des produits de vêtements/mode (catégorie Mode)
        $fashionProducts = Product::whereHas('category', function($query) {
            $query->where('name', 'Mode');
        })->limit(2)->get();

        foreach ($fashionProducts as $index => $product) {
            $colorSubset = $colorValues->take(3); // 3 couleurs
            $sizeSubset = $sizeValues; // Toutes les tailles

            $variantNumber = 0;
            foreach ($colorSubset as $color) {
                foreach ($sizeSubset as $size) {
                    $variantNumber++;

                    // Variation de prix basée sur la taille
                    $priceModifier = match($size->value) {
                        'S' => 0,
                        'M' => 5,
                        'L' => 10,
                        'XL' => 15,
                        default => 0,
                    };

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => strtoupper($product->sku . '-' . $color->slug . '-' . $size->slug),
                        'price' => $product->price + $priceModifier,
                        'compare_at_price' => $product->price + $priceModifier + 20,
                        'stock_quantity' => rand(5, 50),
                        'attributes' => [
                            'couleur' => $color->value,
                            'taille' => $size->value,
                        ],
                        'attribute_value_ids' => [$color->id, $size->id],
                        'is_default' => $variantNumber === 1, // Premier variant est le défaut
                        'is_active' => true,
                        'position' => $variantNumber,
                    ]);
                }
            }

            $this->command->info("✓ Créé " . ($colorSubset->count() * $sizeSubset->count()) . " variantes pour le produit: {$product->name}");
        }

        // Produit avec couleurs uniquement
        $techProducts = Product::whereHas('category', function($query) {
            $query->where('name', 'Électronique');
        })->limit(1)->get();

        foreach ($techProducts as $product) {
            $colorSubset = $colorValues->take(4); // 4 couleurs

            $variantNumber = 0;
            foreach ($colorSubset as $color) {
                $variantNumber++;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper($product->sku . '-' . $color->slug),
                    'price' => $product->price,
                    'stock_quantity' => rand(10, 30),
                    'attributes' => [
                        'couleur' => $color->value,
                    ],
                    'attribute_value_ids' => [$color->id],
                    'is_default' => $variantNumber === 1,
                    'is_active' => true,
                    'position' => $variantNumber,
                ]);
            }

            $this->command->info("✓ Créé {$colorSubset->count()} variantes pour le produit: {$product->name}");
        }
    }
}
