# Guide du Système de Notifications Email

## 📧 Vue d'Ensemble

Le système de notifications email de la plateforme Dropshipping Tunisia envoie automatiquement des emails pour tous les événements importants du cycle de vie des commandes et de la gestion des utilisateurs.

## ✅ Notifications Implémentées

### Pour les Clients

1. **OrderConfirmation** - Confirmation de commande
   - Envoyé après la création d'une commande
   - Contient le numéro de commande, montant, mode de paiement
   - Action: Voir ma commande

2. **OrderStatusUpdated** - Mise à jour du statut de commande
   - Envoyé quand le statut change (confirmée, annulée, etc.)
   - Messages contextuels selon le nouveau statut
   - Action: Voir ma commande

3. **ShipmentNotification** - Notification d'expédition
   - Envoyé quand une commande est expédiée
   - Inclut le numéro de suivi et le transporteur
   - Envoyé aussi lors des mises à jour du tracking
   - Action: Suivre ma commande

### Pour les Fournisseurs

4. **NewOrderNotification** - Nouvelle commande reçue
   - Envoyé quand une commande contient des produits du fournisseur
   - Affiche uniquement les articles concernés
   - Montant total pour le fournisseur
   - Action: Voir la commande

5. **ProductApproved** - Produit approuvé
   - Envoyé quand l'admin approuve un produit
   - Confirme que le produit est visible sur la plateforme
   - Action: Voir le produit

6. **ProductRejected** - Produit rejeté
   - Envoyé quand l'admin rejette un produit
   - Inclut la raison du rejet
   - Action: Modifier le produit

7. **SupplierApproved** - Compte fournisseur approuvé
   - Envoyé quand l'admin approuve un nouveau fournisseur
   - Liste les prochaines étapes
   - Action: Accéder au tableau de bord

8. **SupplierSuspended** - Compte fournisseur suspendu
   - Envoyé quand l'admin suspend un fournisseur
   - Inclut la raison de la suspension
   - Explique les conséquences

## 🔧 Configuration Email

### 1. Configuration .env

Configurez votre service d'envoi d'emails dans `.env`:

#### Option 1: Gmail (Développement)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dropshipping.tn
MAIL_FROM_NAME="Dropshipping Tunisia"
```

#### Option 2: Mailtrap (Tests)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dropshipping.tn
MAIL_FROM_NAME="Dropshipping Tunisia"
```

#### Option 3: SendGrid (Production)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dropshipping.tn
MAIL_FROM_NAME="Dropshipping Tunisia"
```

#### Option 4: Mailgun (Production)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.dropshipping.tn
MAILGUN_SECRET=your-mailgun-api-key
MAIL_FROM_ADDRESS=noreply@dropshipping.tn
MAIL_FROM_NAME="Dropshipping Tunisia"
```

### 2. Configuration de Queue (Recommandé)

Les notifications implémentent `ShouldQueue` pour un envoi asynchrone.

#### Installer Redis (Recommandé pour Production)
```bash
# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl start redis

# Configuration .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Ou utiliser Database Queue (Alternative)
```bash
# Créer la table jobs
php artisan queue:table
php artisan migrate

# Configuration .env
QUEUE_CONNECTION=database
```

#### Démarrer le worker
```bash
# Développement
php artisan queue:work

# Production (avec supervisor)
php artisan queue:work --daemon --tries=3
```

### 3. Configuration Supervisor (Production)

Créer `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/dropshipping/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/dropshipping/storage/logs/worker.log
stopwaitsecs=3600
```

Activer:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 📝 Utilisation dans le Code

### Envoyer une notification

```php
use App\Notifications\OrderConfirmation;

// À un utilisateur
$user->notify(new OrderConfirmation($order));

// À plusieurs utilisateurs
$suppliers->each(function($supplier) use ($order) {
    $supplier->notify(new NewOrderNotification($order));
});
```

### Créer une nouvelle notification

```bash
php artisan make:notification NomNotification
```

Exemple de notification:

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExempleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $data)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sujet de l\'email')
            ->greeting("Bonjour {$notifiable->name},")
            ->line('Votre message ici')
            ->action('Bouton d\'action', url('/'))
            ->salutation('L\'équipe Dropshipping Tunisia');
    }

    public function toArray(object $notifiable): array
    {
        return [
            // Données pour notification database (si activé)
        ];
    }
}
```

## 🎨 Personnalisation des Templates Email

### Publier les templates Laravel
```bash
php artisan vendor:publish --tag=laravel-mail
```

Les templates se trouvent dans `resources/views/vendor/mail/html/`.

### Personnaliser le logo et les couleurs

Modifier `config/mail.php`:

```php
'markdown' => [
    'theme' => 'default',
    'paths' => [
        resource_path('views/vendor/mail'),
    ],
],
```

Créer `resources/views/vendor/mail/html/themes/dropshipping.css`:

```css
/* Couleurs de la marque */
:root {
    --primary: #3B82F6;
    --secondary: #10B981;
}

.button-primary {
    background-color: var(--primary);
}
```

## 🧪 Tests

### Test manuel avec Tinker

```bash
php artisan tinker
```

```php
// Récupérer un utilisateur
$user = User::find(1);

// Envoyer une notification de test
$order = Order::first();
$user->notify(new \App\Notifications\OrderConfirmation($order));
```

### Test avec Mailtrap

1. Créer un compte sur [mailtrap.io](https://mailtrap.io)
2. Configurer les credentials dans `.env`
3. Envoyer des emails - ils seront capturés par Mailtrap

### Test unitaire

```php
use Illuminate\Support\Facades\Notification;

public function test_order_confirmation_sent()
{
    Notification::fake();

    // Créer une commande
    $order = Order::factory()->create();

    // Vérifier que la notification a été envoyée
    Notification::assertSentTo(
        $order->user,
        OrderConfirmation::class
    );
}
```

## 📊 Statistiques des Notifications

Les notifications en queue sont trackées dans:
- `jobs` table - Jobs en attente
- `failed_jobs` table - Jobs échoués

### Voir les jobs échoués
```bash
php artisan queue:failed
```

### Retry des jobs échoués
```bash
# Retry un job spécifique
php artisan queue:retry [job-id]

# Retry tous les jobs échoués
php artisan queue:retry all
```

## 🔐 Sécurité

### Éviter le spam
- Rate limiting sur les notifications (optionnel)
- Unsubscribe links (à implémenter)
- Préférences utilisateur (à implémenter)

### RGPD/Conformité
- Les emails contiennent uniquement les informations nécessaires
- Pas de données sensibles (mots de passe, numéros de carte)
- Les utilisateurs peuvent désactiver les notifications (à implémenter)

## 🚀 Prochaines Améliorations

### Court Terme
1. **Notifications SMS** (API SMS tunisienne)
   - Confirmation de commande
   - Code de suivi d'expédition
   - Livraison

2. **Notifications Database**
   - Cloche de notifications dans l'interface
   - Centre de notifications

3. **Préférences utilisateur**
   - Choisir quelles notifications recevoir
   - Canal préféré (email/SMS)

### Moyen Terme
4. **Templates email personnalisés**
   - Builder visuel
   - Variables dynamiques
   - A/B testing

5. **Statistiques d'engagement**
   - Taux d'ouverture
   - Taux de clic
   - Dashboard analytics

6. **Notifications Push**
   - Pour application mobile future
   - Web push notifications

## 📚 Ressources

- [Laravel Notifications Documentation](https://laravel.com/docs/11.x/notifications)
- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Laravel Queues Documentation](https://laravel.com/docs/11.x/queues)

## ⚠️ Troubleshooting

### Les emails ne sont pas envoyés

1. Vérifier la configuration `.env`
2. Vérifier que le worker de queue tourne
3. Vérifier les logs: `storage/logs/laravel.log`
4. Tester avec `php artisan tinker`

### Les emails arrivent en spam

1. Configurer SPF records
2. Configurer DKIM
3. Utiliser un service d'emailing professionnel (SendGrid, Mailgun)
4. Éviter les mots spam dans les sujets

### Les emails sont lents

1. Utiliser les queues (déjà implémenté)
2. Augmenter le nombre de workers
3. Utiliser Redis au lieu de database queue

---

**Créé le:** 2025-01-XX
**Dernière mise à jour:** 2025-01-XX
**Version:** 1.0
