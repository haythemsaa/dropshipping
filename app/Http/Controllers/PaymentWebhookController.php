<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Webhook e-Dinar
     */
    public function edinarWebhook(Request $request)
    {
        Log::info('e-Dinar webhook received', $request->all());

        try {
            // Vérifier la signature
            $signature = $request->header('X-Signature') ?? $request->input('signature');
            if (!$this->paymentService->verifySignature('edinar', $request->all(), $signature)) {
                Log::warning('e-Dinar webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Traiter le callback
            $result = $this->paymentService->handleCallback('edinar', $request->all());

            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 400);
            }

            // Mettre à jour le paiement et la commande
            $this->processPaymentCallback($result);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('e-Dinar webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Webhook Clictopay
     */
    public function clictopayWebhook(Request $request)
    {
        Log::info('Clictopay webhook received', $request->all());

        try {
            // Vérifier la signature
            $signature = $request->header('X-Signature') ?? $request->input('signature');
            if (!$this->paymentService->verifySignature('card', $request->all(), $signature)) {
                Log::warning('Clictopay webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Traiter le callback
            $result = $this->paymentService->handleCallback('card', $request->all());

            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 400);
            }

            // Mettre à jour le paiement et la commande
            $this->processPaymentCallback($result);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Clictopay webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Webhook Konnect
     */
    public function konnectWebhook(Request $request)
    {
        Log::info('Konnect webhook received', $request->all());

        try {
            // Vérifier la signature
            $signature = $request->header('X-Signature') ?? $request->input('signature');
            if (!$this->paymentService->verifySignature('konnect', $request->all(), $signature)) {
                Log::warning('Konnect webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Traiter le callback
            $result = $this->paymentService->handleCallback('konnect', $request->all());

            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 400);
            }

            // Mettre à jour le paiement et la commande
            $this->processPaymentCallback($result);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Konnect webhook processing failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Page de retour après paiement réussi
     */
    public function paymentReturn(Request $request, string $gateway)
    {
        try {
            $result = $this->paymentService->handleCallback($gateway, $request->all());

            if ($result['success'] && $result['paid']) {
                $order = Order::find($result['order_id'] ?? null);

                if ($order) {
                    return redirect()->route('orders.confirmation', $order)
                        ->with('success', 'Paiement effectué avec succès!');
                }
            }

            return redirect()->route('cart.index')
                ->with('error', 'Une erreur est survenue lors du paiement.');

        } catch (\Exception $e) {
            Log::error('Payment return handling failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Une erreur est survenue lors du paiement.');
        }
    }

    /**
     * Page de retour après annulation de paiement
     */
    public function paymentCancel(Request $request, string $gateway)
    {
        Log::info('Payment cancelled', [
            'gateway' => $gateway,
            'data' => $request->all()
        ]);

        return redirect()->route('cart.index')
            ->with('warning', 'Paiement annulé. Vous pouvez réessayer quand vous voulez.');
    }

    /**
     * Traite le callback de paiement et met à jour la commande
     */
    private function processPaymentCallback(array $result): void
    {
        DB::beginTransaction();
        try {
            // Trouver le paiement via transaction_id
            $payment = Payment::where('transaction_id', $result['transaction_id'])->first();

            if (!$payment) {
                // Essayer de trouver via order_id
                $order = isset($result['order_id'])
                    ? Order::find($result['order_id'])
                    : Order::where('order_number', $result['order_number'] ?? '')->first();

                if ($order) {
                    $payment = $order->payment;
                }
            }

            if (!$payment) {
                throw new \Exception('Payment not found for transaction ' . $result['transaction_id']);
            }

            $order = $payment->order;
            $oldStatus = $order->status;

            // Mettre à jour le paiement
            $payment->update([
                'status' => $result['paid'] ? 'completed' : 'failed',
                'payment_details' => array_merge(
                    $payment->payment_details ?? [],
                    [
                        'callback_status' => $result['status'],
                        'callback_at' => now()->toIso8601String(),
                        'card_last4' => $result['card_last4'] ?? null,
                        'card_brand' => $result['card_brand'] ?? null,
                        'payment_method_used' => $result['payment_method'] ?? null,
                    ]
                ),
            ]);

            // Mettre à jour la commande si paiement réussi
            if ($result['paid']) {
                $order->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);

                // Mettre à jour les items en "processing"
                $order->items()->update(['status' => 'processing']);

                // Notifier le client
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus));
            } else {
                // Paiement échoué - remettre le stock
                foreach ($order->items as $item) {
                    $item->product->incrementStock($item->quantity);
                }

                $order->update(['status' => 'cancelled']);
                $order->items()->update(['status' => 'cancelled']);

                // Annuler les commissions
                foreach ($order->items as $item) {
                    $item->commission()->update(['status' => 'cancelled']);
                }
            }

            DB::commit();

            Log::info('Payment callback processed successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'paid' => $result['paid'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

