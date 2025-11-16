<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use SkipsFailures;

    private $supplierId;
    private $importedCount = 0;
    private $skippedCount = 0;

    public function __construct(int $supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Ignorer les lignes vides
        if (empty($row['nom']) || empty($row['prix'])) {
            $this->skippedCount++;
            return null;
        }

        // Trouver la catégorie
        $category = null;
        if (!empty($row['categorie'])) {
            $category = Category::where('name', 'LIKE', '%' . $row['categorie'] . '%')->first();
        }

        if (!$category) {
            // Utiliser la catégorie "Autres" par défaut
            $category = Category::where('slug', 'autres')->first();
        }

        // Générer le SKU si non fourni
        $sku = !empty($row['sku'])
            ? $row['sku']
            : 'SKU-' . strtoupper(Str::random(8));

        // Vérifier si le SKU existe déjà
        if (Product::where('sku', $sku)->exists()) {
            $sku = $sku . '-' . time();
        }

        // Générer le slug
        $slug = Str::slug($row['nom']);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $this->importedCount++;

        return new Product([
            'supplier_id' => $this->supplierId,
            'category_id' => $category->id,
            'name' => $row['nom'],
            'slug' => $slug,
            'sku' => $sku,
            'description' => $row['description'] ?? '',
            'price' => (float)$row['prix'],
            'stock_quantity' => isset($row['stock']) ? (int)$row['stock'] : 0,
            'stock_status' => isset($row['stock']) && (int)$row['stock'] > 0 ? 'in_stock' : 'out_of_stock',
            'status' => 'inactive', // Sera activé après approbation
            'approved_at' => null, // Nécessite approbation admin
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nom.required' => 'Le nom du produit est obligatoire',
            'prix.required' => 'Le prix est obligatoire',
            'prix.numeric' => 'Le prix doit être un nombre',
            'prix.min' => 'Le prix doit être positif',
        ];
    }

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'failed' => count($this->failures()),
        ];
    }
}
