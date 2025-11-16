<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway e-Dinar (D17 - Poste Tunisienne)
 *
 * Documentation: https://www.poste.tn/e-dinar
 * Type: Monnaie électronique tunisienne
 * Frais: ~1-2% par transaction
 */
class EDinarGateway implements PaymentGatewayInterface
{
    private string $apiUrl;
    private string $merchantId;
    private string $secretKey;
    private bool $testMode;

    public function __construct()
    {
        $this->testMode = config('payment.edinar.test_mode', true);
        $this->apiUrl = $this->testMode
            ? config('payment.edinar.test_url', 'https://test.edinar.poste.tn/api')
            : config('payment.edinar.live_url', 'https://edinar.poste.tn/api');

        $this->merchantId = config('payment.edinar.merchant_id');
        $this->secretKey = config('payment.edinar.secret_key');
    }

    public function getName(): string
    {
        return 'e-Dinar (D17)';
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function initializePayment(Order $order, array $options = []): array
    {
        try {
            // Générer un ID de transaction unique
            $transactionId = 'EDIN-' . time() . '-' . $order->id;

            // Préparer les données de paiement
            $paymentData = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'currency' => 'TND',
                'order_id' => $order->order_number,
                'customer_email' => $order->user->email,
                'customer_phone' => $order->user->phone ?? '',
                'return_url' => route('payment.callback.edinar.return'),
                'cancel_url' => route('payment.callback.edinar.cancel'),
                'notify_url' => route('payment.callback.edinar.notify'),
                'description' => "Commande #{$order->order_number}",
            ];

            // Ajouter la signature
            $paymentData['signature'] = $this->generateSignature($paymentData);

            // TODO: Appel API réel à e-Dinar
            // $response = Http::post($this->apiUrl . '/payment/init', $paymentData);

            // Simulation pour développement
            if ($this->testMode) {
                $redirectUrl = $this->apiUrl . '/payment?token=' . base64_encode(json_encode($paymentData));

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
            //         'redirect_url' => $data['payment_url'],
            //         'transaction_id' => $transactionId,
            //         'error' => null,
            //     ];
            // }

            return [
                'success' => false,
                'redirect_url' => null,
                'transaction_id' => null,
                'error' => 'e-Dinar API not configured',
            ];

        } catch (\Exception $e) {
            Log::error('e-Dinar payment initialization failed', [
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
            // $response = Http::post($this->apiUrl . '/payment/status', [
            //     'merchant_id' => $this->merchantId,
            //     'transaction_id' => $transactionId,
            //     'signature' => $this->generateSignature(['transaction_id' => $transactionId]),
            // ]);

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
            Log::error('e-Dinar status check failed', [
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
            preg_match('/EDIN-\d+-(\d+)/', $data['transaction_id'], $matches);
            $orderId = $matches[1] ?? null;

            if (!$orderId) {
                throw new \Exception('Invalid transaction ID format');
            }

            // Déterminer si le paiement est réussi
            $isPaid = in_array($data['status'], ['completed', 'success', 'paid']);

            return [
                'success' => true,
                'transaction_id' => $data['transaction_id'],
                'amount' => $data['amount'] ?? 0,
                'order_id' => $orderId,
                'status' => $data['status'],
                'paid' => $isPaid,
            ];

        } catch (\Exception $e) {
            Log::error('e-Dinar callback handling failed', [
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
        $expectedSignature = $this->generateSignature($data);
        return hash_equals($expectedSignature, $signature);
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            // TODO: Appel API pour remboursement
            // $response = Http::post($this->apiUrl . '/payment/refund', [
            //     'merchant_id' => $this->merchantId,
            //     'transaction_id' => $payment->transaction_id,
            //     'amount' => $refundAmount,
            //     'signature' => $this->generateSignature([...]),
            // ]);

            Log::info('e-Dinar refund requested', [
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'error' => 'Refund API not implemented yet',
            ];

        } catch (\Exception $e) {
            Log::error('e-Dinar refund failed', [
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

    /**
     * Génère une signature pour sécuriser les communications
     */
    private function generateSignature(array $data): string
    {
        // Retirer la signature si elle existe déjà
        unset($data['signature']);

        // Trier les clés
        ksort($data);

        // Créer la chaîne à signer
        $signString = '';
        foreach ($data as $key => $value) {
            $signString .= $key . '=' . $value . '&';
        }
        $signString .= 'secret=' . $this->secretKey;

        // Générer le hash
        return hash('sha256', $signString);
    }
}
