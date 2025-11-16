<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdminOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = Order::query()
            ->with(['user', 'payment', 'items']);

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

        // Filtrer par mode de paiement
        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
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
            'Email',
            'Téléphone',
            'Nb Articles',
            'Sous-total',
            'Frais Livraison',
            'Total',
            'Mode Paiement',
            'Statut Paiement',
            'Statut Commande',
            'Ville Livraison',
            'Date Confirmation',
            'Date Livraison',
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->user->name,
            $order->user->email,
            $order->user->phone ?? '-',
            $order->items->count(),
            number_format($order->subtotal, 2),
            number_format($order->shipping_fee, 2),
            number_format($order->total_amount, 2),
            $this->getPaymentMethodLabel($order->payment_method),
            $order->payment ? $this->getPaymentStatusLabel($order->payment->status) : '-',
            $this->getOrderStatusLabel($order->status),
            $order->shippingAddress?->city ?? '-',
            $order->confirmed_at?->format('d/m/Y H:i') ?? '-',
            $order->delivered_at?->format('d/m/Y H:i') ?? '-',
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
                    'startColor' => ['rgb' => '10B981'],
                ],
            ],
        ];
    }

    private function getPaymentMethodLabel(string $method): string
    {
        return match($method) {
            'card' => 'Carte bancaire',
            'edinar' => 'e-Dinar',
            'cod' => 'À la livraison',
            default => $method,
        };
    }

    private function getPaymentStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Complété',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            default => $status,
        };
    }

    private function getOrderStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }
}
