<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway Konnect
 *
 * Documentation: https://api.konnect.network/docs
 * Type: Wallet mobile et cartes bancaires
 * Frais: ~1.5-2.5% par transaction
 * Avantage: Populaire en Tunisie, interface moderne
 */
class KonnectGateway implements PaymentGatewayInterface
{
    private string $apiUrl;
    private string $apiKey;
    private string $walletId;
    private bool $testMode;

    public function __construct()
    {
        $this->testMode = config('payment.konnect.test_mode', true);
        $this->apiUrl = $this->testMode
            ? config('payment.konnect.test_url', 'https://api.preprod.konnect.network/api/v2')
            : config('payment.konnect.live_url', 'https://api.konnect.network/api/v2');

        $this->apiKey = config('payment.konnect.api_key');
        $this->walletId = config('payment.konnect.wallet_id');
    }

    public function getName(): string
    {
        return 'Konnect';
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function initializePayment(Order $order, array $options = []): array
    {
        try {
            // Générer un ID de transaction unique
            $transactionId = 'KON-' . time() . '-' . $order->id;

            // Préparer les données de paiement
            $paymentData = [
                'receiverWalletId' => $this->walletId,
                'token' => 'TND',
                'amount' => (int)($order->total_amount * 1000), // Montant en millimes
                'type' => 'immediate',
                'description' => "Commande #{$order->order_number}",
                'acceptedPaymentMethods' => ['wallet', 'bank_card', 'e-DINAR'],
                'lifespan' => 10, // Minutes
                'checkoutForm' => true,
                'addPaymentFeesToAmount' => true,
                'firstName' => explode(' ', $order->user->name)[0] ?? '',
                'lastName' => explode(' ', $order->user->name)[1] ?? '',
                'phoneNumber' => $order->user->phone ?? '',
                'email' => $order->user->email,
                'orderId' => $order->order_number,
                'webhook' => route('payment.callback.konnect.webhook'),
                'silentWebhook' => true,
                'successUrl' => route('payment.callback.konnect.success'),
                'failUrl' => route('payment.callback.konnect.fail'),
                'theme' => 'light',
            ];

            // TODO: Appel API réel à Konnect
            // $response = Http::withHeaders([
            //     'x-api-key' => $this->apiKey,
            //     'Content-Type' => 'application/json',
            // ])->post($this->apiUrl . '/payments/init-payment', $paymentData);

            // Simulation pour développement
            if ($this->testMode) {
                $paymentRef = 'KON' . substr(md5($transactionId), 0, 16);
                $redirectUrl = $this->apiUrl . '/payments/gateway/' . $paymentRef;

                Log::info('Konnect payment initialized (test mode)', [
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                    'payment_ref' => $paymentRef,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'transaction_id' => $transactionId,
                    'payment_ref' => $paymentRef,
                    'error' => null,
                ];
            }

            // En production, parser la vraie réponse
            // if ($response->successful()) {
            //     $data = $response->json();
            //     return [
            //         'success' => true,
            //         'redirect_url' => $data['payUrl'],
            //         'transaction_id' => $transactionId,
            //         'payment_ref' => $data['paymentRef'],
            //         'error' => null,
            //     ];
            // }

            return [
                'success' => false,
                'redirect_url' => null,
                'transaction_id' => null,
                'error' => 'Konnect API not configured',
            ];

        } catch (\Exception $e) {
            Log::error('Konnect payment initialization failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'redirect_url' => null,
                'transaction_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        try {
            // TODO: Appel API pour vérifier le statut
            // $response = Http::withHeaders([
            //     'x-api-key' => $this->apiKey,
            // ])->get($this->apiUrl . '/payments/' . $transactionId);

            // Simulation
            return [
                'status' => 'pending',
                'paid' => false,
                'details' => [
                    'transaction_id' => $transactionId,
                    'message' => 'Status check not implemented yet',
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Konnect status check failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'paid' => false,
                'details' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function handleCallback(array $data): array
    {
        try {
            // Vérifier les données requises
            if (!isset($data['payment_ref'])) {
                throw new \Exception('Missing payment_ref in callback');
            }

            // Extraire l'order number
            $orderNumber = $data['orderId'] ?? null;
            if (!$orderNumber) {
                throw new \Exception('Missing orderId in callback');
            }

            // Statuts Konnect
            $isPaid = ($data['status'] ?? '') === 'completed';

            Log::info('Konnect callback received', [
                'payment_ref' => $data['payment_ref'],
                'status' => $data['status'] ?? 'unknown',
                'order_number' => $orderNumber,
            ]);

            return [
                'success' => true,
                'transaction_id' => $data['payment_ref'],
                'amount' => ($data['amount'] ?? 0) / 1000, // Convertir millimes en dinars
                'order_number' => $orderNumber,
                'status' => $data['status'] ?? 'unknown',
                'paid' => $isPaid,
                'payment_method' => $data['payment_method'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Konnect callback handling failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'transaction_id' => null,
                'amount' => 0,
                'order_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifySignature(array $data, string $signature): bool
    {
        // Konnect utilise une signature basée sur l'API key
        $signString = $data['payment_ref'] . ':' . $data['orderId'] . ':' . $this->apiKey;
        $expectedSignature = hash('sha256', $signString);

        return hash_equals($expectedSignature, $signature);
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            // TODO: Appel API pour remboursement
            // $response = Http::withHeaders([
            //     'x-api-key' => $this->apiKey,
            // ])->post($this->apiUrl . '/payments/' . $payment->transaction_id . '/refund', [
            //     'amount' => (int)($refundAmount * 1000),
            // ]);

            Log::info('Konnect refund requested', [
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'error' => 'Refund API not implemented yet',
            ];

        } catch (\Exception $e) {
            Log::error('Konnect refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
