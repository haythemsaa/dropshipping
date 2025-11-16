<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Électronique',
                'slug' => 'electronique',
                'description' => 'Smartphones, tablettes, ordinateurs et accessoires électroniques',
                'is_active' => true,
                'order' => 1,
                'subcategories' => [
                    ['name' => 'Smartphones', 'slug' => 'smartphones', 'order' => 1],
                    ['name' => 'Tablettes', 'slug' => 'tablettes', 'order' => 2],
                    ['name' => 'Ordinateurs', 'slug' => 'ordinateurs', 'order' => 3],
                    ['name' => 'Accessoires', 'slug' => 'accessoires-electronique', 'order' => 4],
                ]
            ],
            [
                'name' => 'Mode & Vêtements',
                'slug' => 'mode-vetements',
                'description' => 'Vêtements hommes, femmes, enfants et accessoires de mode',
                'is_active' => true,
                'order' => 2,
                'subcategories' => [
                    ['name' => 'Homme', 'slug' => 'homme', 'order' => 1],
                    ['name' => 'Femme', 'slug' => 'femme', 'order' => 2],
                    ['name' => 'Enfant', 'slug' => 'enfant', 'order' => 3],
                    ['name' => 'Chaussures', 'slug' => 'chaussures', 'order' => 4],
                    ['name' => 'Sacs & Accessoires', 'slug' => 'sacs-accessoires', 'order' => 5],
                ]
            ],
            [
                'name' => 'Maison & Décoration',
                'slug' => 'maison-decoration',
                'description' => 'Meubles, décoration, électroménager et articles pour la maison',
                'is_active' => true,
                'order' => 3,
                'subcategories' => [
                    ['name' => 'Meubles', 'slug' => 'meubles', 'order' => 1],
                    ['name' => 'Décoration', 'slug' => 'decoration', 'order' => 2],
                    ['name' => 'Électroménager', 'slug' => 'electromenager', 'order' => 3],
                    ['name' => 'Cuisine', 'slug' => 'cuisine', 'order' => 4],
                ]
            ],
            [
                'name' => 'Beauté & Santé',
                'slug' => 'beaute-sante',
                'description' => 'Produits de beauté, cosmétiques et articles de santé',
                'is_active' => true,
                'order' => 4,
                'subcategories' => [
                    ['name' => 'Maquillage', 'slug' => 'maquillage', 'order' => 1],
                    ['name' => 'Soins de la peau', 'slug' => 'soins-peau', 'order' => 2],
                    ['name' => 'Parfums', 'slug' => 'parfums', 'order' => 3],
                    ['name' => 'Santé & Bien-être', 'slug' => 'sante-bien-etre', 'order' => 4],
                ]
            ],
            [
                'name' => 'Sports & Loisirs',
                'slug' => 'sports-loisirs',
                'description' => 'Équipements sportifs et articles de loisirs',
                'is_active' => true,
                'order' => 5,
                'subcategories' => [
                    ['name' => 'Fitness', 'slug' => 'fitness', 'order' => 1],
                    ['name' => 'Sports d\'extérieur', 'slug' => 'sports-exterieur', 'order' => 2],
                    ['name' => 'Camping', 'slug' => 'camping', 'order' => 3],
                ]
            ],
            [
                'name' => 'Bébé & Enfant',
                'slug' => 'bebe-enfant',
                'description' => 'Articles pour bébés et enfants',
                'is_active' => true,
                'order' => 6,
                'subcategories' => [
                    ['name' => 'Vêtements bébé', 'slug' => 'vetements-bebe', 'order' => 1],
                    ['name' => 'Jouets', 'slug' => 'jouets', 'order' => 2],
                    ['name' => 'Puériculture', 'slug' => 'puericulture', 'order' => 3],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $subcategories = $categoryData['subcategories'] ?? [];
            unset($categoryData['subcategories']);

            $category = \App\Models\Category::create($categoryData);

            foreach ($subcategories as $subcat) {
                \App\Models\Category::create(array_merge($subcat, [
                    'parent_id' => $category->id,
                    'is_active' => true,
                    'description' => null,
                ]));
            }
        }
    }
}
