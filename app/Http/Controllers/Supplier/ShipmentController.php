<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\OrderItem;
use App\Notifications\ShipmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Affiche la liste des expéditions
     */
    public function index(Request $request)
    {
        $supplier = auth()->user();

        $query = Shipment::whereHas('orderItem', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })->with(['orderItem.order.user', 'orderItem.product']);

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('supplier.shipments.index', compact('shipments'));
    }

    /**
     * Crée une expédition pour un item de commande
     */
    public function create(Request $request, OrderItem $orderItem)
    {
        // Vérifier que l'item appartient au fournisseur
        if ($orderItem->supplier_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que l'item est en traitement
        if ($orderItem->status !== 'processing') {
            return back()->with('error', 'L\'item doit être accepté avant de créer une expédition.');
        }

        // Vérifier qu'il n'y a pas déjà une expédition
        if ($orderItem->shipment) {
            return back()->with('error', 'Une expédition existe déjà pour cet item.');
        }

        $validated = $request->validate([
            'carrier' => 'required|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $shipment = Shipment::create([
                'order_item_id' => $orderItem->id,
                'carrier' => $validated['carrier'],
                'tracking_number' => $validated['tracking_number'] ?? null,
                'status' => 'pending',
                'estimated_delivery_date' => $validated['estimated_delivery_date'] ?? null,
                'tracking_history' => json_encode([
                    [
                        'status' => 'pending',
                        'description' => 'Expédition créée',
                        'timestamp' => now()->toIso8601String(),
                    ]
                ]),
            ]);

            // Mettre à jour le statut de l'item
            $orderItem->update(['status' => 'shipped']);

            DB::commit();

            // Envoyer notification au client avec info de tracking
            $shipment->load('orderItem.order');
            $customer = $shipment->orderItem->order->user;
            $customer->notify(new ShipmentNotification($shipment));

            return back()->with('success', 'Expédition créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de l\'expédition.');
        }
    }

    /**
     * Met à jour une expédition
     */
    public function update(Request $request, Shipment $shipment)
    {
        // Vérifier que l'expédition appartient au fournisseur
        if ($shipment->orderItem->supplier_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'carrier' => 'required|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'status' => 'required|in:pending,in_transit,delivered,failed,returned',
            'estimated_delivery_date' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $shipment->update($validated);

            // Mettre à jour le statut de l'order item si livré
            if ($validated['status'] === 'delivered') {
                $shipment->orderItem->update([
                    'status' => 'delivered',
                    'delivered_at' => $validated['delivered_at'] ?? now()
                ]);

                // Mettre à jour la commission en "approved"
                $shipment->orderItem->commission()->update(['status' => 'approved']);
            }

            DB::commit();

            return back()->with('success', 'Expédition mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Met à jour le tracking de l'expédition
     */
    public function updateTracking(Request $request, Shipment $shipment)
    {
        // Vérifier que l'expédition appartient au fournisseur
        if ($shipment->orderItem->supplier_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'location' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Ajouter à l'historique de tracking
            $history = json_decode($shipment->tracking_history ?? '[]', true);
            $history[] = [
                'status' => $validated['status'],
                'description' => $validated['description'],
                'location' => $validated['location'] ?? null,
                'timestamp' => now()->toIso8601String(),
            ];

            $shipment->update([
                'tracking_history' => json_encode($history)
            ]);

            DB::commit();

            // Envoyer notification au client avec mise à jour
            $shipment->load('orderItem.order');
            $customer = $shipment->orderItem->order->user;
            $customer->notify(new ShipmentNotification($shipment));

            return back()->with('success', 'Suivi mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du suivi.');
        }
    }
}
