<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('role', 'admin')->first();

        // Produits pour TechStore Tunisia (Électronique)
        $techStore = \App\Models\User::where('email', 'supplier1@example.tn')->first();
        $smartphoneCategory = \App\Models\Category::where('slug', 'smartphones')->first();
        $tablettesCategory = \App\Models\Category::where('slug', 'tablettes')->first();

        $techProducts = [
            [
                'name' => 'Samsung Galaxy A54 5G',
                'slug' => 'samsung-galaxy-a54-5g',
                'description' => 'Smartphone Samsung Galaxy A54 5G avec écran Super AMOLED 6.4", 128GB de stockage, appareil photo 50MP',
                'price' => 1299.00,
                'cost_price' => 1100.00,
                'stock_quantity' => 25,
                'category_id' => $smartphoneCategory->id,
                'brand' => 'Samsung',
                'sku' => 'SAM-A54-BLK',
            ],
            [
                'name' => 'iPhone 14 128GB',
                'slug' => 'iphone-14-128gb',
                'description' => 'Apple iPhone 14 avec puce A15 Bionic, écran Super Retina XDR 6.1", caméra 12MP',
                'price' => 3499.00,
                'cost_price' => 3200.00,
                'stock_quantity' => 15,
                'category_id' => $smartphoneCategory->id,
                'brand' => 'Apple',
                'sku' => 'APL-IP14-128',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'slug' => 'samsung-galaxy-tab-s9',
                'description' => 'Tablette Samsung Galaxy Tab S9, écran 11", 128GB, Wi-Fi + 5G, S Pen inclus',
                'price' => 1899.00,
                'cost_price' => 1650.00,
                'stock_quantity' => 20,
                'category_id' => $tablettesCategory->id,
                'brand' => 'Samsung',
                'sku' => 'SAM-TABS9-GRY',
                'is_featured' => true,
            ],
        ];

        foreach ($techProducts as $productData) {
            \App\Models\Product::create(array_merge($productData, [
                'supplier_id' => $techStore->id,
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'shipping_days' => 2,
                'shipping_cost' => 7.00,
                'low_stock_threshold' => 5,
            ]));
        }

        // Produits pour Mode Chic (Mode & Vêtements)
        $modeChic = \App\Models\User::where('email', 'supplier2@example.tn')->first();
        $hommeCategory = \App\Models\Category::where('slug', 'homme')->first();
        $femmeCategory = \App\Models\Category::where('slug', 'femme')->first();

        $modeProducts = [
            [
                'name' => 'Chemise Homme Coton - Bleu',
                'slug' => 'chemise-homme-coton-bleu',
                'description' => 'Chemise élégante pour homme en coton premium, coupe slim fit, disponible en plusieurs tailles',
                'price' => 89.90,
                'cost_price' => 65.00,
                'stock_quantity' => 50,
                'category_id' => $hommeCategory->id,
                'brand' => 'Mode Chic',
                'sku' => 'MC-CHM-BLU-M',
            ],
            [
                'name' => 'Robe d\'été Femme - Fleurie',
                'slug' => 'robe-ete-femme-fleurie',
                'description' => 'Belle robe d\'été pour femme avec motifs floraux, tissu léger et confortable',
                'price' => 129.00,
                'cost_price' => 90.00,
                'stock_quantity' => 35,
                'category_id' => $femmeCategory->id,
                'brand' => 'Mode Chic',
                'sku' => 'MC-ROB-FLR-M',
                'is_featured' => true,
            ],
            [
                'name' => 'Jean Homme Slim Fit',
                'slug' => 'jean-homme-slim-fit',
                'description' => 'Jean pour homme coupe slim fit, denim de qualité, bleu foncé',
                'price' => 149.00,
                'cost_price' => 110.00,
                'stock_quantity' => 40,
                'category_id' => $hommeCategory->id,
                'brand' => 'Mode Chic',
                'sku' => 'MC-JEN-SLM-32',
            ],
        ];

        foreach ($modeProducts as $productData) {
            \App\Models\Product::create(array_merge($productData, [
                'supplier_id' => $modeChic->id,
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'shipping_days' => 3,
                'shipping_cost' => 5.00,
                'low_stock_threshold' => 10,
            ]));
        }

        // Produits pour Maison & Déco
        $maisonDeco = \App\Models\User::where('email', 'supplier3@example.tn')->first();
        $decorationCategory = \App\Models\Category::where('slug', 'decoration')->first();
        $cuisineCategory = \App\Models\Category::where('slug', 'cuisine')->first();

        $maisonProducts = [
            [
                'name' => 'Lampe de Table Moderne',
                'slug' => 'lampe-table-moderne',
                'description' => 'Lampe de table au design moderne, finition chromée, éclairage LED',
                'price' => 199.00,
                'cost_price' => 145.00,
                'stock_quantity' => 30,
                'category_id' => $decorationCategory->id,
                'brand' => 'HomeStyle',
                'sku' => 'MD-LMP-MOD-CHR',
            ],
            [
                'name' => 'Set Couteaux de Cuisine 6 Pièces',
                'slug' => 'set-couteaux-cuisine-6-pieces',
                'description' => 'Ensemble de 6 couteaux de cuisine professionnels en acier inoxydable avec support',
                'price' => 159.00,
                'cost_price' => 115.00,
                'stock_quantity' => 45,
                'category_id' => $cuisineCategory->id,
                'brand' => 'ChefPro',
                'sku' => 'MD-CUT-SET-6',
                'is_featured' => true,
            ],
            [
                'name' => 'Coussin Décoratif 40x40cm',
                'slug' => 'coussin-decoratif-40x40',
                'description' => 'Coussin décoratif en velours avec motifs géométriques, plusieurs couleurs disponibles',
                'price' => 39.90,
                'cost_price' => 25.00,
                'stock_quantity' => 100,
                'category_id' => $decorationCategory->id,
                'brand' => 'Comfort Home',
                'sku' => 'MD-CUS-VEL-40',
            ],
        ];

        foreach ($maisonProducts as $productData) {
            \App\Models\Product::create(array_merge($productData, [
                'supplier_id' => $maisonDeco->id,
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'shipping_days' => 4,
                'shipping_cost' => 8.00,
                'low_stock_threshold' => 15,
            ]));
        }
    }
}
