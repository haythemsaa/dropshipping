<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\EDinarGateway;
use App\Services\Payment\Gateways\ClictopayGateway;
use App\Services\Payment\Gateways\KonnectGateway;
use Exception;

class PaymentService
{
    /**
     * Gateways disponibles
     */
    private array $gateways = [];

    public function __construct()
    {
        $this->registerGateways();
    }

    /**
     * Enregistre tous les gateways disponibles
     */
    private function registerGateways(): void
    {
        $this->gateways = [
            'edinar' => new EDinarGateway(),
            'card' => new ClictopayGateway(),
            'konnect' => new KonnectGateway(),
        ];
    }

    /**
     * Récupère un gateway par son nom
     *
     * @param string $name
     * @return PaymentGatewayInterface
     * @throws Exception
     */
    public function getGateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new Exception("Gateway '{$name}' not found");
        }

        return $this->gateways[$name];
    }

    /**
     * Initialise un paiement
     *
     * @param Order $order
     * @param string $method Méthode de paiement (edinar, card, cod)
     * @param array $options
     * @return array
     */
    public function initializePayment(Order $order, string $method, array $options = []): array
    {
        // COD ne nécessite pas de gateway
        if ($method === 'cod') {
            return [
                'success' => true,
                'method' => 'cod',
                'redirect_url' => null,
            ];
        }

        // Déterminer le gateway approprié
        $gatewayName = $this->getGatewayForMethod($method);
        $gateway = $this->getGateway($gatewayName);

        // Initialiser le paiement
        $result = $gateway->initializePayment($order, $options);

        // Mettre à jour le paiement avec transaction ID
        if ($result['success'] && isset($result['transaction_id'])) {
            $order->payment->update([
                'transaction_id' => $result['transaction_id'],
                'gateway' => $gatewayName,
            ]);
        }

        return $result;
    }

    /**
     * Vérifie le statut d'un paiement
     *
     * @param Payment $payment
     * @return array
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        if (!$payment->gateway || !$payment->transaction_id) {
            return [
                'status' => 'unknown',
                'paid' => false,
                'details' => [],
            ];
        }

        $gateway = $this->getGateway($payment->gateway);
        return $gateway->checkPaymentStatus($payment->transaction_id);
    }

    /**
     * Traite un callback de paiement
     *
     * @param string $gatewayName
     * @param array $data
     * @return array
     */
    public function handleCallback(string $gatewayName, array $data): array
    {
        $gateway = $this->getGateway($gatewayName);
        return $gateway->handleCallback($data);
    }

    /**
     * Vérifie la signature d'un callback
     *
     * @param string $gatewayName
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function verifySignature(string $gatewayName, array $data, string $signature): bool
    {
        $gateway = $this->getGateway($gatewayName);
        return $gateway->verifySignature($data, $signature);
    }

    /**
     * Rembourse un paiement
     *
     * @param Payment $payment
     * @param float|null $amount
     * @return array
     */
    public function refund(Payment $payment, ?float $amount = null): array
    {
        if (!$payment->gateway) {
            return [
                'success' => false,
                'error' => 'No gateway associated with this payment',
            ];
        }

        $gateway = $this->getGateway($payment->gateway);
        return $gateway->refund($payment, $amount);
    }

    /**
     * Détermine le gateway à utiliser selon la méthode
     *
     * @param string $method
     * @return string
     */
    private function getGatewayForMethod(string $method): string
    {
        return match($method) {
            'edinar' => 'edinar',
            'card' => 'card',
            'konnect' => 'konnect',
            default => 'card',
        };
    }

    /**
     * Liste tous les gateways disponibles
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        return array_map(function($gateway) {
            return [
                'name' => $gateway->getName(),
                'test_mode' => $gateway->isTestMode(),
            ];
        }, $this->gateways);
    }
}
