<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Exports\AdminOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Compter par statut
        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.product.images',
            'items.supplier',
            'items.shipment',
            'shippingAddress',
            'billingAddress',
            'payment'
        ]);

        // Grouper les items par fournisseur
        $itemsBySupplier = $order->items->groupBy('supplier_id');

        return view('admin.orders.show', compact('order', 'itemsBySupplier'));
    }

    /**
     * Annule une commande
     */
    public function cancel(Order $order)
    {
        // Vérifier que la commande peut être annulée
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cette commande ne peut pas être annulée.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour le statut
            $order->update(['status' => 'cancelled']);

            // Remettre le stock pour tous les items
            foreach ($order->items as $item) {
                $item->product->incrementStock($item->quantity);
                $item->update(['status' => 'cancelled']);

                // Annuler la commission
                if ($item->commission) {
                    $item->commission->update(['status' => 'cancelled']);
                }
            }

            // Annuler le paiement si nécessaire
            if ($order->payment && !in_array($order->payment->status, ['completed', 'refunded'])) {
                $order->payment->update(['status' => 'cancelled']);
            }

            // TODO: Notifier le client et les fournisseurs

            DB::commit();

            return back()->with('success', 'Commande annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        DB::beginTransaction();
        try {
            $order->update(['status' => $validated['status']]);

            // TODO: Notifier le client

            DB::commit();

            return back()->with('success', 'Statut de la commande mis à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Export des commandes en Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'payment_method', 'date_from', 'date_to']);

        return Excel::download(
            new AdminOrdersExport($filters),
            'commandes_' . date('Y-m-d') . '.xlsx'
        );
    }
}
