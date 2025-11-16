# Plateforme de Dropshipping - Marketplace Tunisie

## 📋 Description

Plateforme web de dropshipping destinée au marché tunisien, permettant aux vendeurs (revendeurs) sans budget de vendre des produits de grossistes locaux. La plateforme gère l'ensemble du processus : commandes, paiements, expéditions et commissions.

### Concept Principal

- **Vendeurs/Clients** : Peuvent parcourir le catalogue de produits des grossistes et les vendre à leurs propres clients
- **Grossistes/Fournisseurs** : Ajoutent leurs produits, gèrent les stocks et expédient les commandes
- **Plateforme** : Centralise les transactions, gère les paiements et prend une commission sur chaque vente
- **Suivi complet** : Toutes les commandes sont suivies avec numéros de tracking et statuts en temps réel

## ✨ Fonctionnalités Principales

### Pour les Clients/Vendeurs
- ✅ Inscription et authentification sécurisée
- ✅ Navigation par catégories hiérarchiques
- ✅ Recherche avancée de produits
- ✅ Panier d'achat avec gestion de plusieurs fournisseurs
- ✅ Passage de commande simplifié
- ✅ Suivi des commandes en temps réel
- ✅ Historique des achats
- ✅ Gestion des adresses de livraison

### Pour les Fournisseurs/Grossistes
- ✅ Inscription avec validation administrateur
- ✅ Gestion complète du catalogue produits
- ✅ Import de produits en masse (CSV/Excel)
- ✅ Gestion des stocks en temps réel
- ✅ Réception et traitement des commandes
- ✅ Mise à jour des expéditions et tracking
- ✅ Tableau de bord avec statistiques de ventes
- ✅ Gestion des informations bancaires pour paiements

### Pour les Administrateurs
- ✅ Validation des comptes fournisseurs
- ✅ Gestion des catégories de produits
- ✅ Modération des produits
- ✅ Suivi global des commandes
- ✅ Gestion des paiements et commissions
- ✅ Statistiques de la plateforme
- ✅ Configuration des taux de commission
- ✅ Support client

### Système de Paiement
- 💳 **Cartes bancaires tunisiennes** (CIB, Visa, MasterCard locales)
- 💰 **e-Dinar** (Porte-monnaie électronique de la Poste Tunisienne)
- 📦 **Paiement à la livraison** (COD - Cash on Delivery)
- 🔒 Transactions sécurisées via HTTPS
- 💵 Monnaie : Dinar Tunisien (TND)

### Système de Notifications
- ✉️ **Email** : Confirmations de commande, expéditions, mises à jour
- 📱 **SMS** : Alertes importantes pour clients et fournisseurs
- 🔔 Notifications personnalisables par utilisateur

### Gestion des Commissions
- 💼 Taux de commission configurable par fournisseur
- 💰 Calcul automatique des montants (fournisseur vs plateforme)
- 📊 Suivi des paiements aux fournisseurs
- 📈 Rapports de commissions

## 🗄️ Structure de la Base de Données

### Tables Principales

#### `users` - Utilisateurs Multi-rôles
- Rôles : `client`, `supplier` (fournisseur), `admin`
- Statuts : `active`, `pending`, `suspended`, `rejected`
- Informations professionnelles pour les fournisseurs
- Coordonnées bancaires
- Préférences de notifications

#### `categories` - Catégories de Produits
- Structure hiérarchique (catégories parentes et sous-catégories)
- Slug pour URL conviviales
- Ordre personnalisable
- Activation/désactivation

#### `products` - Produits
- Lié au fournisseur
- Informations complètes (prix, stock, poids, dimensions)
- Statuts : `draft`, `active`, `inactive`, `out_of_stock`
- Support des produits vedettes
- Compteurs de vues et ventes
- Approbation administrateur

#### `product_images` - Images de Produits
- Images multiples par produit
- Image principale
- Ordre personnalisable

#### `orders` - Commandes
- Numéro de commande unique
- Multi-statuts selon le cycle de vie
- Support multi-fournisseurs dans une commande
- Calcul automatique des totaux, frais de port, taxes

#### `order_items` - Articles de Commande
- Un item par produit commandé
- Lié au fournisseur pour expédition
- Calcul de commission par item
- Statut indépendant par item

#### `addresses` - Adresses
- Types : facturation et livraison
- Liées aux utilisateurs et aux commandes
- Support des gouvernorats tunisiens
- Adresse par défaut

#### `payments` - Paiements
- Méthodes : carte, e-Dinar, COD
- Transaction IDs et réponses de passerelle
- Gestion des remboursements

#### `shipments` - Expéditions
- Numéro de tracking
- Transporteurs tunisiens (La Poste, Aramex, Rapid-Post, etc.)
- Historique de suivi en JSON
- Dates estimées et réelles de livraison

#### `commissions` - Commissions
- Calcul par order_item
- Taux et montants
- Statuts de paiement
- Suivi des versements aux fournisseurs

#### `cart` & `cart_items` - Panier
- Support utilisateurs connectés et invités (session)
- Sauvegarde du prix au moment de l'ajout

## 🚀 Installation et Configuration

### Prérequis

- PHP 8.4+
- Composer
- Base de données (MySQL, PostgreSQL ou SQLite)
- Node.js & NPM (pour les assets frontend)
- Serveur web (Apache/Nginx)

### Installation

1. **Cloner le repository**
```bash
git clone <repository-url>
cd dropshipping
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Installer les dépendances JavaScript**
```bash
npm install
```

4. **Configuration de l'environnement**
```bash
cp .env.example .env
```

Éditer le fichier `.env` :
```env
APP_NAME="Dropshipping Tunisia"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votredomaine.tn

# Base de données
DB_CONNECTION=mysql  # ou pgsql pour PostgreSQL
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dropshipping
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

# Email (configurez votre service SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@votredomaine.tn"
MAIL_FROM_NAME="${APP_NAME}"
```

5. **Générer la clé d'application**
```bash
php artisan key:generate
```

6. **Créer la base de données**
```bash
# MySQL
mysql -u root -p -e "CREATE DATABASE dropshipping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# PostgreSQL
createdb dropshipping
```

7. **Exécuter les migrations**
```bash
php artisan migrate
```

8. **Créer un compte administrateur**
```bash
php artisan tinker
```
Puis dans Tinker :
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'status' => 'active',
    'email_verified_at' => now(),
]);
```

9. **Créer le lien symbolique pour le storage**
```bash
php artisan storage:link
```

10. **Compiler les assets**
```bash
# Développement
npm run dev

# Production
npm run build
```

11. **Configurer les permissions**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🔧 Configuration des Paiements

### Intégration des Passerelles de Paiement

#### 1. Cartes Bancaires Tunisiennes
Contactez une banque tunisienne ou un PSP (Payment Service Provider) agréé pour obtenir :
- Clés API de paiement
- URL de la passerelle
- Certificats SSL si nécessaires

Configurez dans `.env` :
```env
PAYMENT_GATEWAY_URL=https://payment.example.tn
PAYMENT_MERCHANT_ID=votre_merchant_id
PAYMENT_API_KEY=votre_api_key
PAYMENT_SECRET=votre_secret
```

#### 2. e-Dinar
Contactez la Poste Tunisienne (D17) pour :
- Obtenir un compte marchand e-Dinar
- Recevoir les identifiants API

Configurez dans `.env` :
```env
EDINAR_API_URL=https://api.edinar.tn
EDINAR_MERCHANT_ID=votre_merchant_id
EDINAR_API_KEY=votre_api_key
```

#### 3. Paiement à la Livraison (COD)
Aucune configuration technique nécessaire. Configurez simplement :
- Les transporteurs acceptant le COD
- Les procédures de collecte des fonds
- Les délais de reversement

### Configuration SMS

Pour les notifications SMS, intégrez un service SMS tunisien :

```env
SMS_PROVIDER=example_sms
SMS_API_URL=https://sms.example.tn/api
SMS_API_KEY=votre_api_key
SMS_SENDER_NAME=VotrePlateforme
```

## 📦 Déploiement en Production

### Serveur Web

#### Apache (.htaccess déjà inclus)
```apache
<VirtualHost *:80>
    ServerName votredomaine.tn
    DocumentRoot /var/www/dropshipping/public

    <Directory /var/www/dropshipping/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dropshipping-error.log
    CustomLog ${APACHE_LOG_DIR}/dropshipping-access.log combined
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name votredomaine.tn;
    root /var/www/dropshipping/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### SSL/HTTPS (Obligatoire pour les Paiements)

```bash
# Avec Let's Encrypt (gratuit)
sudo certbot --nginx -d votredomaine.tn
# ou
sudo certbot --apache -d votredomaine.tn
```

### Optimisations de Production

```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation de l'autoloader Composer
composer install --optimize-autoloader --no-dev
```

### Tâches Planifiées (Cron)

Ajoutez au crontab :
```bash
* * * * * cd /var/www/dropshipping && php artisan schedule:run >> /dev/null 2>&1
```

### File d'Attente (Queue Worker)

Configurez un supervisor pour les jobs :

```ini
[program:dropshipping-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/dropshipping/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/dropshipping/storage/logs/worker.log
```

## 🎨 Personnalisation

### Logo et Branding

Placez vos fichiers dans :
- Logo : `public/images/logo.png`
- Favicon : `public/favicon.ico`
- Images de marque : `resources/images/`

### Couleurs et Styles

Modifiez `resources/css/app.css` et `tailwind.config.js` pour personnaliser les couleurs, polices et styles.

### Templates Email

Les templates email se trouvent dans `resources/views/emails/`

## 📊 Utilisation de la Plateforme

### Flux Utilisateur Client

1. **Inscription** → Compte client créé immédiatement
2. **Navigation** → Parcourir les produits par catégorie ou recherche
3. **Ajout au panier** → Produits de différents fournisseurs possibles
4. **Commande** → Saisie adresse de livraison
5. **Paiement** → Choix du mode de paiement (carte/e-Dinar/COD)
6. **Confirmation** → Email/SMS de confirmation
7. **Suivi** → Tracking des colis par fournisseur
8. **Réception** → Livraison et paiement si COD

### Flux Fournisseur

1. **Inscription** → Demande avec justificatifs
2. **Attente validation** → Admin approuve le compte
3. **Activation** → Email de confirmation
4. **Ajout produits** → Création manuelle ou import CSV
5. **Réception commande** → Notification email/SMS
6. **Préparation** → Mise à jour du statut
7. **Expédition** → Saisie du numéro de tracking
8. **Paiement** → Réception des fonds (après commission)

### Flux Administrateur

1. **Validation fournisseurs** → Approuver ou rejeter les demandes
2. **Gestion catalogue** → Créer catégories, modérer produits
3. **Suivi commandes** → Vue globale, support client
4. **Gestion commissions** → Configuration des taux, suivi des paiements
5. **Statistiques** → Dashboard avec métriques clés

## 🛠️ Maintenance

### Sauvegardes

```bash
# Base de données
mysqldump -u user -p dropshipping > backup_$(date +%Y%m%d).sql

# Fichiers
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/
```

### Mises à Jour

```bash
git pull origin main
composer install --no-dev
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Logs

Consultez les logs dans `storage/logs/laravel.log`

## 📝 Développement Futur

### Fonctionnalités Prévues
- [ ] Application mobile native (iOS/Android)
- [ ] Programme de fidélité clients
- [ ] Système d'avis et notations produits
- [ ] Chat en direct support client
- [ ] Multi-devises et expédition internationale
- [ ] API publique pour intégrations tierces
- [ ] Espace marketing pour promotions
- [ ] Analyse avancée et reporting

## 🤝 Support et Contact

Pour toute question ou assistance :
- Documentation : [URL de la doc]
- Email : support@votredomaine.tn
- Téléphone : +216 XX XXX XXX

## 📜 Licence

Cette application est développée spécifiquement pour le marché tunisien.

---

**Développé avec ❤️ pour le e-commerce tunisien**
