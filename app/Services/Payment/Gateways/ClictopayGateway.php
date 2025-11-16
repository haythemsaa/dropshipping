<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway Clictopay (SMT - Société Monétique Tunisie)
 *
 * Documentation: https://www.clictopay.com/fr/api-documentation
 * Type: Cartes bancaires tunisiennes (CIB) et internationales
 * Frais: ~2-3% par transaction
 */
class ClictopayGateway implements PaymentGatewayInterface
{
    private string $apiUrl;
    private string $merchantId;
    private string $apiKey;
    private bool $testMode;

    public function __construct()
    {
        $this->testMode = config('payment.clictopay.test_mode', true);
        $this->apiUrl = $this->testMode
            ? config('payment.clictopay.test_url', 'https://test.clictopay.com/api/v2')
            : config('payment.clictopay.live_url', 'https://secure.clictopay.com/api/v2');

        $this->merchantId = config('payment.clictopay.merchant_id');
        $this->apiKey = config('payment.clictopay.api_key');
    }

    public function getName(): string
    {
        return 'Clictopay (Carte Bancaire)';
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function initializePayment(Order $order, array $options = []): array
    {
        try {
            // Générer un ID de transaction unique
            $transactionId = 'CTP-' . time() . '-' . $order->id;

            // Préparer les données de paiement
            $paymentData = [
                'merchant_id' => $this->merchantId,
                'order_id' => $order->order_number,
                'amount' => (int)($order->total_amount * 1000), // Montant en millimes
                'currency' => 'TND',
                'transaction_id' => $transactionId,
                'customer' => [
                    'email' => $order->user->email,
                    'phone' => $order->user->phone ?? '',
                    'first_name' => explode(' ', $order->user->name)[0] ?? '',
                    'last_name' => explode(' ', $order->user->name)[1] ?? '',
                ],
                'billing_address' => [
                    'address' => $order->billingAddress->address_line_1 ?? '',
                    'city' => $order->billingAddress->city ?? '',
                    'postal_code' => $order->billingAddress->postal_code ?? '',
                    'country' => 'TN',
                ],
                'return_url' => route('payment.callback.clictopay.return'),
                'cancel_url' => route('payment.callback.clictopay.cancel'),
                'webhook_url' => route('payment.callback.clictopay.webhook'),
                'description' => "Commande #{$order->order_number}",
                'language' => 'fr',
            ];

            // TODO: Appel API réel à Clictopay
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $this->apiKey,
            //     'Content-Type' => 'application/json',
            // ])->post($this->apiUrl . '/payments', $paymentData);

            // Simulation pour développement
            if ($this->testMode) {
                $redirectUrl = $this->apiUrl . '/checkout?session=' . base64_encode(json_encode($paymentData));

                Log::info('Clictopay payment initialized (test mode)', [
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'transaction_id' => $transactionId,
                    'error' => null,
                ];
            }

            // En production, parser la vraie réponse
            // if ($response->successful()) {
            //     $data = $response->json();
            //     return [
            //         'success' => true,
            //         'redirect_url' => $data['checkout_url'],
            //         'transaction_id' => $transactionId,
            //         'error' => null,
            //     ];
            // }

            return [
                'success' => false,
                'redirect_url' => null,
                'transaction_id' => null,
                'error' => 'Clictopay API not configured',
            ];

        } catch (\Exception $e) {
            Log::error('Clictopay payment initialization failed', [
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
            //     'Authorization' => 'Bearer ' . $this->apiKey,
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
            Log::error('Clictopay status check failed', [
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
            if (!isset($data['transaction_id']) || !isset($data['status'])) {
                throw new \Exception('Missing required callback data');
            }

            // Extraire l'order_id du transaction_id
            preg_match('/CTP-\d+-(\d+)/', $data['transaction_id'], $matches);
            $orderId = $matches[1] ?? null;

            if (!$orderId) {
                throw new \Exception('Invalid transaction ID format');
            }

            // Statuts Clictopay
            $isPaid = in_array($data['status'], ['completed', 'success', 'approved', 'captured']);

            Log::info('Clictopay callback received', [
                'transaction_id' => $data['transaction_id'],
                'status' => $data['status'],
                'order_id' => $orderId,
            ]);

            return [
                'success' => true,
                'transaction_id' => $data['transaction_id'],
                'amount' => ($data['amount'] ?? 0) / 1000, // Convertir millimes en dinars
                'order_id' => $orderId,
                'status' => $data['status'],
                'paid' => $isPaid,
                'card_last4' => $data['card_last4'] ?? null,
                'card_brand' => $data['card_brand'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Clictopay callback handling failed', [
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
        // Reconstruire la chaîne à vérifier selon la doc Clictopay
        $signString = implode('|', [
            $data['transaction_id'] ?? '',
            $data['order_id'] ?? '',
            $data['amount'] ?? '',
            $data['status'] ?? '',
            $this->apiKey,
        ]);

        $expectedSignature = hash('sha256', $signString);
        return hash_equals($expectedSignature, $signature);
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            // TODO: Appel API pour remboursement
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $this->apiKey,
            // ])->post($this->apiUrl . '/payments/' . $payment->transaction_id . '/refund', [
            //     'amount' => (int)($refundAmount * 1000), // Millimes
            //     'reason' => 'Customer refund request',
            // ]);

            Log::info('Clictopay refund requested', [
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'error' => 'Refund API not implemented yet',
            ];

        } catch (\Exception $e) {
            Log::error('Clictopay refund failed', [
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
