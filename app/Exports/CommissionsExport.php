<?php

namespace App\Exports;

use App\Models\Commission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CommissionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Commission::query()
            ->with(['supplier', 'orderItem.order', 'orderItem.product']);

        // Filtrer par fournisseur
        if (!empty($this->filters['supplier_id'])) {
            $query->where('supplier_id', $this->filters['supplier_id']);
        }

        // Filtrer par statut
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filtrer par période
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'N° Commande',
            'Fournisseur',
            'Produit',
            'Quantité',
            'Prix Unitaire',
            'Sous-total Vente',
            'Taux Commission (%)',
            'Montant Commission',
            'Montant Fournisseur',
            'Statut',
            'Date Approbation',
            'Date Paiement',
        ];
    }

    /**
     * @param Commission $commission
     * @return array
     */
    public function map($commission): array
    {
        $orderItem = $commission->orderItem;

        return [
            $commission->created_at->format('d/m/Y'),
            $orderItem->order->order_number,
            $commission->supplier->business_name ?? $commission->supplier->name,
            $orderItem->product_name,
            $orderItem->quantity,
            number_format($orderItem->price, 2),
            number_format($orderItem->subtotal, 2),
            number_format($commission->rate, 2),
            number_format($commission->amount, 2),
            number_format($orderItem->subtotal - $commission->amount, 2),
            $this->getStatusLabel($commission->status),
            $commission->approved_at?->format('d/m/Y') ?? '-',
            $commission->paid_at?->format('d/m/Y') ?? '-',
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
                    'startColor' => ['rgb' => 'F59E0B'],
                ],
            ],
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'paid' => 'Payée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }
}
