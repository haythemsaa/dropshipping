# Guide d'Intégration des Paiements - Dropshipping Tunisia

## 📋 Vue d'Ensemble

Le système de paiement de la plateforme Dropshipping Tunisia supporte les principales méthodes de paiement tunisiennes :

- **💳 Cartes Bancaires** (CIB et internationales) via Clictopay/SMT
- **💰 e-Dinar** (D17 - Poste Tunisienne)
- **📱 Konnect** (Wallet mobile et cartes)
- **🚚 Paiement à la livraison (COD)**

## 🏗️ Architecture

### Structure du Système

```
app/Services/Payment/
├── Contracts/
│   └── PaymentGatewayInterface.php    # Interface pour tous les gateways
├── Gateways/
│   ├── EDinarGateway.php              # e-Dinar (D17)
│   ├── ClictopayGateway.php           # Cartes bancaires (SMT)
│   └── KonnectGateway.php             # Konnect wallet
└── PaymentService.php                 # Service principal
```

### Flux de Paiement

```
1. Client passe commande → OrderController::store()
2. Redirection vers passerelle → PaymentService::initializePayment()
3. Client effectue paiement sur la page du gateway
4. Callback webhook → PaymentWebhookController
5. Mise à jour commande et paiement
6. Notification client → OrderStatusUpdated
```

## ⚙️ Configuration

### 1. Variables d'Environnement

Ajoutez ces lignes à votre `.env` :

```env
# Configuration Générale
PAYMENT_DEFAULT_GATEWAY=clictopay
PAYMENT_LOGGING=true

# e-Dinar (D17 - Poste Tunisienne)
EDINAR_TEST_MODE=true
EDINAR_MERCHANT_ID=your_merchant_id
EDINAR_SECRET_KEY=your_secret_key
EDINAR_TEST_URL=https://test.edinar.poste.tn/api
EDINAR_LIVE_URL=https://edinar.poste.tn/api

# Clictopay (SMT)
CLICTOPAY_TEST_MODE=true
CLICTOPAY_MERCHANT_ID=your_merchant_id
CLICTOPAY_API_KEY=your_api_key
CLICTOPAY_TEST_URL=https://test.clictopay.com/api/v2
CLICTOPAY_LIVE_URL=https://secure.clictopay.com/api/v2

# Konnect
KONNECT_TEST_MODE=true
KONNECT_API_KEY=your_api_key
KONNECT_WALLET_ID=your_wallet_id
KONNECT_TEST_URL=https://api.preprod.konnect.network/api/v2
KONNECT_LIVE_URL=https://api.konnect.network/api/v2

# Cash on Delivery
COD_ENABLED=true
COD_MAX_AMOUNT=500
COD_EXTRA_FEE=7
```

### 2. Migration Database

Exécutez la migration pour ajouter les champs nécessaires :

```bash
php artisan migrate
```

Cette migration ajoute à la table `payments` :
- `gateway` - Nom du gateway utilisé
- `transaction_id` - ID de transaction du gateway
- `payment_ref` - Référence de paiement (certains gateways)
- `payment_details` - JSON avec détails supplémentaires

## 🔌 Inscription aux Passerelles

### e-Dinar (D17)

**Site:** https://www.poste.tn/e-dinar

**Processus:**
1. Créer un compte marchand sur le portail D17
2. Fournir: Registre de commerce, Patente, RIB
3. Signer le contrat marchand
4. Recevoir les credentials (Merchant ID, Secret Key)
5. Tester en environnement de test
6. Demander l'activation en production

**Frais:** ~1-2% par transaction
**Délai:** 2-3 semaines

### Clictopay / SMT

**Site:** https://www.clictopay.com

**Processus:**
1. Contacter SMT (Société Monétique Tunisie)
2. Fournir: Registre commerce, Patente, Autorisation bancaire
3. Signer contrat TPE virtuel
4. Recevoir credentials API
5. Intégration et tests
6. Certification et mise en production

**Frais:** ~2-3% par transaction
**Délai:** 3-4 semaines

### Konnect

**Site:** https://www.konnect.network

**Processus:**
1. S'inscrire sur https://portal.konnect.network
2. Vérifier l'identité (CIN, Patente)
3. Créer un wallet marchand
4. Obtenir API key
5. Configuration et tests
6. Activation production

**Frais:** ~1.5-2.5% par transaction
**Délai:** 1-2 semaines
**Avantages:** Interface moderne, adoption en croissance

## 💻 Utilisation du Code

### Initialiser un Paiement

```php
use App\Services\Payment\PaymentService;

$paymentService = new PaymentService();

// Initialiser le paiement
$result = $paymentService->initializePayment($order, 'card', [
    // Options supplémentaires si nécessaire
]);

if ($result['success']) {
    // Rediriger vers la page de paiement
    return redirect($result['redirect_url']);
} else {
    // Gérer l'erreur
    return back()->with('error', $result['error']);
}
```

### Vérifier le Statut d'un Paiement

```php
$status = $paymentService->checkPaymentStatus($payment);

if ($status['paid']) {
    // Paiement réussi
    echo "Paiement confirmé";
} else {
    echo "Statut: " . $status['status'];
}
```

### Rembourser un Paiement

```php
$result = $paymentService->refund($payment, 50.00); // Montant optionnel

if ($result['success']) {
    echo "Remboursement effectué: " . $result['refund_id'];
}
```

## 🔗 Webhooks

### URLs de Callback

Les webhooks sont configurés automatiquement pour chaque paiement :

- **e-Dinar:** `https://your-domain.tn/payment/webhook/edinar`
- **Clictopay:** `https://your-domain.tn/payment/webhook/clictopay`
- **Konnect:** `https://your-domain.tn/payment/webhook/konnect`

### URLs de Retour

- **Succès:** `https://your-domain.tn/payment/return/{gateway}`
- **Annulation:** `https://your-domain.tn/payment/cancel/{gateway}`

### Sécurité des Webhooks

Chaque webhook vérifie la signature de la requête :

```php
public function verifySignature(array $data, string $signature): bool
{
    $expectedSignature = $this->generateSignature($data);
    return hash_equals($expectedSignature, $signature);
}
```

⚠️ **Important:** Les webhooks ne nécessitent pas d'authentification Laravel (pas de middleware `auth`) car ils sont appelés par les gateways de paiement.

## 🧪 Tests

### Mode Test

Tous les gateways sont en mode test par défaut (`*_TEST_MODE=true`).

En mode test :
- Aucun paiement réel n'est effectué
- Les URLs de test sont utilisées
- Des transactions simulées sont créées

### Tester manuellement

```bash
php artisan tinker
```

```php
$order = Order::first();
$service = new \App\Services\Payment\PaymentService();

// Tester e-Dinar
$result = $service->initializePayment($order, 'edinar');
print_r($result);

// Tester Clictopay
$result = $service->initializePayment($order, 'card');
print_r($result);

// Tester Konnect
$result = $service->initializePayment($order, 'konnect');
print_r($result);
```

### Simuler un Webhook

```bash
curl -X POST http://localhost:8000/payment/webhook/clictopay \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": "CTP-1234567890-1",
    "status": "completed",
    "amount": 100000,
    "signature": "test_signature"
  }'
```

## 📊 Base de Données

### Table `payments`

Nouveaux champs ajoutés :

| Champ | Type | Description |
|-------|------|-------------|
| `gateway` | string | Gateway utilisé (edinar, card, konnect) |
| `transaction_id` | string | ID de transaction du gateway |
| `payment_ref` | string | Référence de paiement |
| `payment_details` | json | Détails supplémentaires |

### Exemple de `payment_details`

```json
{
  "callback_status": "completed",
  "callback_at": "2025-01-15T14:30:00Z",
  "card_last4": "1234",
  "card_brand": "Visa",
  "payment_method_used": "bank_card"
}
```

## 🔐 Sécurité

### Bonnes Pratiques

1. **HTTPS Obligatoire:** Les webhooks doivent être en HTTPS en production
2. **Vérification de Signature:** Toujours vérifier la signature des callbacks
3. **Logs:** Activer les logs de paiement (`PAYMENT_LOGGING=true`)
4. **Idempotence:** Les webhooks peuvent être appelés plusieurs fois
5. **Timeout:** Paiements en attente expirés après 15 minutes

### Protection CSRF

Les webhooks sont exclus de la protection CSRF dans `app/Http/Middleware/VerifyCsrfToken.php` :

```php
protected $except = [
    'payment/webhook/*',
];
```

## 🚀 Mise en Production

### Checklist

- [ ] Obtenir les credentials de production pour chaque gateway
- [ ] Mettre `*_TEST_MODE=false` dans `.env`
- [ ] Configurer les URLs de production
- [ ] Tester avec de vraies transactions de test
- [ ] Configurer SSL/HTTPS
- [ ] Vérifier les URLs de callback dans les dashboards des gateways
- [ ] Activer les logs (`PAYMENT_LOGGING=true`)
- [ ] Tester les webhooks avec les outils des gateways
- [ ] Vérifier que les emails de confirmation fonctionnent
- [ ] Configurer la surveillance des paiements échoués

### Configuration Nginx pour Webhooks

```nginx
location /payment/webhook/ {
    proxy_pass http://your-app;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;

    # Augmenter le timeout pour les webhooks
    proxy_read_timeout 60s;
}
```

## 🔍 Debugging

### Logs des Paiements

Les logs sont dans `storage/logs/laravel.log` :

```bash
# Voir les logs en temps réel
tail -f storage/logs/laravel.log | grep -i payment

# Filtrer les webhooks
tail -f storage/logs/laravel.log | grep -i webhook
```

### Problèmes Fréquents

**Webhook non reçu :**
- Vérifier que l'URL est accessible publiquement
- Vérifier les logs du gateway
- Tester avec ngrok en développement : `ngrok http 8000`

**Signature invalide :**
- Vérifier le secret key dans `.env`
- Vérifier l'ordre des paramètres dans la signature
- Consulter la doc du gateway pour le format exact

**Paiement en attente indéfiniment :**
- Vérifier que le webhook est configuré
- Vérifier les logs Laravel
- Utiliser `checkPaymentStatus()` manuellement

## 📱 Paiement Mobile

### e-Dinar Mobile

Les clients peuvent payer avec l'app D17 :
1. Scanner le QR code (généré par le gateway)
2. Confirmer dans l'app D17
3. Callback automatique

### Konnect Wallet

Les clients peuvent utiliser le wallet Konnect :
1. Connexion au wallet
2. Validation avec code SMS
3. Paiement instantané

## 💰 Frais et Limites

| Gateway | Frais | Limites Min/Max | Délai Versement |
|---------|-------|-----------------|-----------------|
| e-Dinar | 1-2% | 1 DT / 5000 DT | J+2 |
| Clictopay | 2-3% | 1 DT / 10000 DT | J+3 |
| Konnect | 1.5-2.5% | 0.5 DT / 5000 DT | J+1 |
| COD | 0% | - / 500 DT | À la livraison |

## 🔄 Remboursements

Les remboursements sont supportés par tous les gateways :

```php
// Remboursement total
$paymentService->refund($payment);

// Remboursement partiel
$paymentService->refund($payment, 50.00);
```

**Délais:**
- **e-Dinar:** 3-5 jours ouvrables
- **Clictopay:** 5-7 jours ouvrables
- **Konnect:** 1-2 jours ouvrables

## 📚 Documentation des Gateways

- **e-Dinar:** https://www.poste.tn/e-dinar/documentation
- **Clictopay:** https://www.clictopay.com/fr/api-documentation
- **Konnect:** https://api.konnect.network/docs
- **SMT:** https://www.smt.tn/documentation

## ⚠️ Notes Importantes

1. **Test en Production:** Faites toujours des transactions de test en production avant le lancement
2. **Support Client:** Prévoyez un support pour les problèmes de paiement
3. **Réconciliation:** Vérifiez régulièrement les paiements dans les dashboards des gateways
4. **Conformité:** Respectez les normes PCI-DSS (pas de stockage de cartes)
5. **Monitoring:** Surveillez les taux de réussite des paiements

## 🆘 Support

### Contacts des Gateways

- **e-Dinar:** support@poste.tn
- **Clictopay:** support@clictopay.com
- **Konnect:** support@konnect.network

### Logs à Fournir

En cas de problème, fournissez :
- Transaction ID
- Order ID
- Timestamp
- Logs Laravel (`storage/logs/laravel.log`)
- Screenshots si nécessaire

---

**Version:** 1.0
**Dernière mise à jour:** 2025-01-XX
**Statut:** ⚠️ En développement - API simulées
