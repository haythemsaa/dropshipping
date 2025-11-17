<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SupplierOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = OrderItem::query()
            ->with(['order.user', 'product', 'variant', 'shipment'])
            ->where('supplier_id', $this->supplierId);

        // Filtrer par statut
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filtrer par période
        if (!empty($this->filters['date_from'])) {
            $query->whereHas('order', function ($q) {
                $q->whereDate('created_at', '>=', $this->filters['date_from']);
            });
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereHas('order', function ($q) {
                $q->whereDate('created_at', '<=', $this->filters['date_to']);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N° Commande',
            'Date',
            'Client',
            'Email Client',
            'Produit',
            'Variante',
            'SKU',
            'Quantité',
            'Prix Unitaire',
            'Sous-total',
            'Commission (%)',
            'Commission (DT)',
            'Montant Fournisseur',
            'Statut',
            'Suivi Expédition',
            'Date Livraison',
        ];
    }

    /**
     * @param OrderItem $orderItem
     * @return array
     */
    public function map($orderItem): array
    {
        return [
            $orderItem->order->order_number,
            $orderItem->created_at->format('d/m/Y H:i'),
            $orderItem->order->user->name,
            $orderItem->order->user->email,
            $orderItem->product_name,
            $orderItem->getFormattedVariantAttributes(true) ?: '-',
            $orderItem->getSku(),
            $orderItem->quantity,
            number_format($orderItem->price, 2),
            number_format($orderItem->subtotal, 2),
            number_format($orderItem->commission_rate, 2),
            number_format($orderItem->commission_amount, 2),
            number_format($orderItem->supplier_amount, 2),
            $this->getStatusLabel($orderItem->status),
            $orderItem->shipment?->tracking_number ?? '-',
            $orderItem->delivered_at?->format('d/m/Y') ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style de l'en-tête
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }

    /**
     * Get status label in French
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }
}
