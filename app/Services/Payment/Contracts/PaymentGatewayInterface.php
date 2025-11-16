<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initialise un paiement et retourne l'URL de redirection
     *
     * @param Order $order
     * @param array $options Options spécifiques au gateway
     * @return array ['success' => bool, 'redirect_url' => string, 'transaction_id' => string, 'error' => string|null]
     */
    public function initializePayment(Order $order, array $options = []): array;

    /**
     * Vérifie le statut d'un paiement
     *
     * @param string $transactionId
     * @return array ['status' => string, 'paid' => bool, 'details' => array]
     */
    public function checkPaymentStatus(string $transactionId): array;

    /**
     * Traite le callback de paiement (webhook ou return URL)
     *
     * @param array $data Données du callback
     * @return array ['success' => bool, 'transaction_id' => string, 'amount' => float, 'order_id' => int]
     */
    public function handleCallback(array $data): array;

    /**
     * Vérifie la signature du callback pour sécurité
     *
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function verifySignature(array $data, string $signature): bool;

    /**
     * Rembourse un paiement
     *
     * @param Payment $payment
     * @param float|null $amount Montant à rembourser (null = total)
     * @return array ['success' => bool, 'refund_id' => string, 'error' => string|null]
     */
    public function refund(Payment $payment, ?float $amount = null): array;

    /**
     * Retourne le nom du gateway
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Retourne si le gateway est en mode test
     *
     * @return bool
     */
    public function isTestMode(): bool;
}
