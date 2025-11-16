<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes de l'utilisateur
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $order->load([
            'items.product.images',
            'items.supplier',
            'items.shipment',
            'shippingAddress',
            'billingAddress',
            'payment'
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Affiche la page de checkout
     */
    public function checkout()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        $cart->load(['items.product.supplier', 'items.product.images']);

        // Vérifier que tous les produits sont disponibles
        foreach ($cart->items as $item) {
            if ($item->product->status !== 'active' || !$item->product->approved_at) {
                return redirect()->route('cart.index')
                    ->with('error', 'Un produit de votre panier n\'est plus disponible.');
            }

            if (!$item->product->isInStock() || $item->product->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Stock insuffisant pour ' . $item->product->name);
            }
        }

        // Récupérer les adresses de l'utilisateur
        $addresses = auth()->user()->addresses;

        return view('orders.checkout', compact('cart', 'addresses'));
    }

    /**
     * Crée une nouvelle commande
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:card,e-dinar,cod',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        DB::beginTransaction();
        try {
            // Vérifier que les adresses appartiennent à l'utilisateur
            $shippingAddress = Address::where('id', $request->shipping_address_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $billingAddress = Address::where('id', $request->billing_address_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Calculer les totaux
            $subtotal = 0;
            $shippingFee = 7.00; // Frais de livraison standard en TND
            $tax = 0;

            $cart->load('items.product.supplier');

            foreach ($cart->items as $item) {
                // Vérifier la disponibilité
                if ($item->product->status !== 'active' || !$item->product->approved_at) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', 'Un produit n\'est plus disponible.');
                }

                if (!$item->product->isInStock() || $item->product->stock_quantity < $item->quantity) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', 'Stock insuffisant pour ' . $item->product->name);
                }

                $subtotal += $item->price * $item->quantity;
            }

            $total = $subtotal + $shippingFee + $tax;

            // Créer la commande
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => $tax,
                'total' => $total,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'notes' => $request->notes,
            ]);

            // Créer les items de commande avec calcul des commissions
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                $supplier = $product->supplier;

                $itemSubtotal = $cartItem->price * $cartItem->quantity;

                // Utiliser le taux de commission du fournisseur
                $commissionRate = $supplier->commission_rate ?? 10.00;
                $commissionAmount = ($itemSubtotal * $commissionRate) / 100;
                $supplierAmount = $itemSubtotal - $commissionAmount;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'supplier_id' => $supplier->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $itemSubtotal,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'supplier_amount' => $supplierAmount,
                    'status' => 'pending',
                ]);

                // Créer l'enregistrement de commission
                Commission::create([
                    'order_item_id' => $orderItem->id,
                    'supplier_id' => $supplier->id,
                    'amount' => $commissionAmount,
                    'rate' => $commissionRate,
                    'status' => 'pending',
                ]);

                // Décrémenter le stock
                $product->decrementStock($cartItem->quantity);

                // Incrémenter le compteur de ventes
                $product->increment('sales_count');
            }

            // Créer le paiement
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'processing',
            ]);

            // Vider le panier
            $cart->clear();

            DB::commit();

            // TODO: Envoyer les notifications par email et SMS
            // TODO: Si paiement par carte ou e-Dinar, rediriger vers la passerelle

            return redirect()->route('orders.confirmation', $order)
                ->with('success', 'Commande créée avec succès!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Une erreur est survenue lors de la création de la commande.');
        }
    }

    /**
     * Affiche la page de confirmation de commande
     */
    public function confirmation(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'items.product.images',
            'items.supplier',
            'shippingAddress',
            'payment'
        ]);

        return view('orders.confirmation', compact('order'));
    }

    /**
     * Annule une commande
     */
    public function cancel(Order $order)
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier que la commande peut être annulée
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour le statut
            $order->update(['status' => 'cancelled']);

            // Remettre le stock
            foreach ($order->items as $item) {
                $item->product->incrementStock($item->quantity);
                $item->update(['status' => 'cancelled']);
            }

            // Annuler le paiement si nécessaire
            if ($order->payment && $order->payment->status !== 'completed') {
                $order->payment->update(['status' => 'cancelled']);
            }

            // Mettre à jour les commissions
            foreach ($order->items as $item) {
                $item->commission()->update(['status' => 'cancelled']);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }
}
