<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes du fournisseur
     */
    public function index(Request $request)
    {
        $supplier = auth()->user();

        $query = OrderItem::where('supplier_id', $supplier->id)
            ->with(['order.user', 'product', 'shipment']);

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Recherche par numéro de commande
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%");
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(20);

        // Compter les items par statut pour les filtres
        $statusCounts = [
            'all' => OrderItem::where('supplier_id', $supplier->id)->count(),
            'pending' => OrderItem::where('supplier_id', $supplier->id)->where('status', 'pending')->count(),
            'processing' => OrderItem::where('supplier_id', $supplier->id)->where('status', 'processing')->count(),
            'shipped' => OrderItem::where('supplier_id', $supplier->id)->where('status', 'shipped')->count(),
            'delivered' => OrderItem::where('supplier_id', $supplier->id)->where('status', 'delivered')->count(),
        ];

        return view('supplier.orders.index', compact('orderItems', 'statusCounts'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $supplier = auth()->user();

        // Récupérer uniquement les items de ce fournisseur pour cette commande
        $orderItems = $order->items()
            ->where('supplier_id', $supplier->id)
            ->with(['product.images', 'shipment'])
            ->get();

        if ($orderItems->isEmpty()) {
            abort(403, 'Cette commande ne contient aucun de vos produits.');
        }

        $order->load(['user', 'shippingAddress', 'billingAddress', 'payment']);

        return view('supplier.orders.show', compact('order', 'orderItems'));
    }

    /**
     * Accepte un item de commande
     */
    public function accept(OrderItem $orderItem)
    {
        // Vérifier que l'item appartient au fournisseur
        if ($orderItem->supplier_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier le statut
        if ($orderItem->status !== 'pending') {
            return back()->with('error', 'Cet item a déjà été traité.');
        }

        DB::beginTransaction();
        try {
            $orderItem->update(['status' => 'processing']);

            // TODO: Envoyer notification au client

            DB::commit();

            return back()->with('success', 'Commande acceptée. Vous pouvez maintenant créer l\'expédition.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Refuse un item de commande
     */
    public function reject(OrderItem $orderItem)
    {
        // Vérifier que l'item appartient au fournisseur
        if ($orderItem->supplier_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier le statut
        if (!in_array($orderItem->status, ['pending', 'processing'])) {
            return back()->with('error', 'Impossible de refuser cet item.');
        }

        DB::beginTransaction();
        try {
            $orderItem->update(['status' => 'cancelled']);

            // Remettre le stock
            $orderItem->product->incrementStock($orderItem->quantity);

            // Annuler la commission
            $orderItem->commission()->update(['status' => 'cancelled']);

            // TODO: Envoyer notification au client et admin

            DB::commit();

            return back()->with('success', 'Item refusé et stock restauré.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue.');
        }
    }
}
