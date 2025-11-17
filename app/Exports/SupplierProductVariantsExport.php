<?php

namespace App\Exports;

use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SupplierProductVariantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = ProductVariant::query()
            ->with(['product'])
            ->whereHas('product', function($q) {
                $q->where('supplier_id', $this->supplierId);
            });

        // Filtrer par statut
        if (!empty($this->filters['status'])) {
            $is_active = $this->filters['status'] === 'active';
            $query->where('is_active', $is_active);
        }

        // Filtrer par stock
        if (isset($this->filters['low_stock']) && $this->filters['low_stock']) {
            $query->where('stock_quantity', '<=', 10);
        }

        // Filtrer par produit
        if (!empty($this->filters['product_id'])) {
            $query->where('product_id', $this->filters['product_id']);
        }

        return $query->orderBy('product_id')->orderBy('position');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Produit',
            'SKU Variante',
            'Attributs',
            'Prix (TND)',
            'Prix Comparaison (TND)',
            'Stock',
            'Statut',
            'Par Défaut',
            'Ventes (30j)',
            'Chiffre d\'Affaires (30j)',
            'Date Création',
        ];
    }

    /**
     * @param ProductVariant $variant
     * @return array
     */
    public function map($variant): array
    {
        // Calculer les ventes des 30 derniers jours
        $sales30Days = $variant->orderItems()
            ->whereHas('order', function($q) {
                $q->where('created_at', '>=', now()->subDays(30))
                  ->whereNotIn('status', ['cancelled']);
            })
            ->sum('quantity');

        $revenue30Days = $variant->orderItems()
            ->whereHas('order', function($q) {
                $q->where('created_at', '>=', now()->subDays(30))
                  ->whereNotIn('status', ['cancelled']);
            })
            ->sum('subtotal');

        return [
            $variant->product->name,
            $variant->sku,
            $variant->getFormattedAttributes(true),
            number_format($variant->price, 2),
            $variant->compare_at_price ? number_format($variant->compare_at_price, 2) : '-',
            $variant->stock_quantity,
            $variant->is_active ? 'Actif' : 'Inactif',
            $variant->is_default ? 'Oui' : 'Non',
            $sales30Days,
            number_format($revenue30Days, 2),
            $variant->created_at->format('d/m/Y'),
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
}
