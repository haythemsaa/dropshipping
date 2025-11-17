<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Commission;
use App\Models\Coupon;
use App\Notifications\OrderConfirmation;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdated;
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
     * Valide un code coupon (AJAX)
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Ce code promo n\'existe pas.',
            ]);
        }

        $error = $coupon->getValidationError(auth()->user(), $request->subtotal);
        if ($error) {
            return response()->json([
                'valid' => false,
                'message' => $error,
            ]);
        }

        // Calculer le panier pour le coupon
        $cart = Cart::where('user_id', auth()->id())->first();
        if (!$cart) {
            return response()->json([
                'valid' => false,
                'message' => 'Panier introuvable.',
            ]);
        }

        $cart->load('items.product');
        $cartItems = [];
        foreach ($cart->items as $item) {
            $cartItems[] = [
                'product' => $item->product,
                'quantity' => $item->quantity,
            ];
        }

        $discount = $coupon->calculateDiscount($request->subtotal, $cartItems);

        if ($discount == 0 && $coupon->type !== 'free_shipping') {
            return response()->json([
                'valid' => false,
                'message' => 'Ce coupon ne s\'applique pas aux produits de votre panier.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Code promo appliqué avec succès!',
            'discount' => $discount,
            'type' => $coupon->type,
            'formatted_discount' => number_format($discount, 2) . ' TND',
        ]);
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
            'coupon_code' => 'nullable|string',
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

            // Traiter le coupon si présent
            $coupon = null;
            $couponDiscount = 0;
            $freeShipping = false;

            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

                if ($coupon) {
                    $error = $coupon->getValidationError(auth()->user(), $subtotal);
                    if ($error) {
                        DB::rollBack();
                        return redirect()->route('orders.checkout')
                            ->with('error', $error);
                    }

                    // Calculer la réduction
                    $cartItems = [];
                    foreach ($cart->items as $item) {
                        $cartItems[] = [
                            'product' => $item->product,
                            'quantity' => $item->quantity,
                        ];
                    }

                    $couponDiscount = $coupon->calculateDiscount($subtotal, $cartItems);

                    if ($coupon->type === 'free_shipping') {
                        $freeShipping = true;
                    }

                    if ($couponDiscount == 0 && !$freeShipping) {
                        DB::rollBack();
                        return redirect()->route('orders.checkout')
                            ->with('error', 'Ce coupon ne s\'applique pas à votre panier.');
                    }
                }
            }

            // Appliquer la réduction
            if ($freeShipping) {
                $shippingFee = 0;
            }

            $total = $subtotal + $shippingFee + $tax - $couponDiscount;

            // Créer la commande
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => auth()->id(),
                'coupon_id' => $coupon ? $coupon->id : null,
                'coupon_code' => $coupon ? $coupon->code : null,
                'coupon_discount' => $couponDiscount,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingFee,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'customer_notes' => $request->notes,
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

            // Enregistrer l'utilisation du coupon
            if ($coupon && ($couponDiscount > 0 || $freeShipping)) {
                $coupon->recordUsage(auth()->user(), $order->id, $couponDiscount);
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

            // Envoyer la notification de confirmation au client
            auth()->user()->notify(new OrderConfirmation($order));

            // Envoyer la notification aux fournisseurs concernés
            $suppliers = $order->items()->with('supplier')->get()
                ->pluck('supplier')
                ->unique('id');

            foreach ($suppliers as $supplier) {
                $supplier->notify(new NewOrderNotification($order));
            }

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
            $oldStatus = $order->status;

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

            // Notifier le client de l'annulation
            auth()->user()->notify(new OrderStatusUpdated($order, $oldStatus));

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }
}
