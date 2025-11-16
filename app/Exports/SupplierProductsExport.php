<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SupplierProductsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $supplierId;
    protected $filters;

    public function __construct(int $supplierId, array $filters = [])
    {
        $this->supplierId = $supplierId;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Product::query()
            ->with(['category'])
            ->where('supplier_id', $this->supplierId);

        // Filtrer par statut
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filtrer par catégorie
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Nom',
            'Catégorie',
            'Prix (DT)',
            'Stock',
            'Statut Stock',
            'Statut',
            'Approuvé',
            'Ventes',
            'Date Création',
            'Date Approbation',
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category->name,
            number_format($product->price, 2),
            $product->stock_quantity,
            $this->getStockStatusLabel($product->stock_status),
            $this->getStatusLabel($product->status),
            $product->approved_at ? 'Oui' : 'Non',
            $product->sales_count ?? 0,
            $product->created_at->format('d/m/Y'),
            $product->approved_at?->format('d/m/Y') ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B5CF6'],
                ],
            ],
        ];
    }

    private function getStockStatusLabel(string $status): string
    {
        return match($status) {
            'in_stock' => 'En stock',
            'out_of_stock' => 'Rupture',
            'low_stock' => 'Stock faible',
            default => $status,
        };
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            default => $status,
        };
    }
}
