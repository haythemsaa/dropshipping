#!/bin/bash

echo "======================================"
echo "Installation de la plateforme Dropshipping"
echo "======================================"
echo ""

# Vérifier si PHP est installé
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé. Veuillez installer PHP 8.4 ou supérieur."
    exit 1
fi

# Vérifier la version de PHP
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP version: $PHP_VERSION"

# Vérifier si Composer est installé
if ! command -v composer &> /dev/null; then
    echo "❌ Composer n'est pas installé. Veuillez installer Composer."
    exit 1
fi
echo "✓ Composer est installé"

# Vérifier si PostgreSQL est installé
if ! command -v psql &> /dev/null; then
    echo "⚠ PostgreSQL n'est pas installé. Vous devrez l'installer manuellement."
fi

echo ""
echo "📦 Installation des dépendances..."
composer install --no-interaction --prefer-dist --optimize-autoloader

if [ ! -f .env ]; then
    echo ""
    echo "⚙️  Création du fichier .env..."
    cp .env.example .env

    echo ""
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate

    echo ""
    echo "⚠️  Veuillez configurer votre base de données dans le fichier .env"
    echo "   - DB_CONNECTION=pgsql"
    echo "   - DB_HOST=127.0.0.1"
    echo "   - DB_PORT=5432"
    echo "   - DB_DATABASE=dropshipping"
    echo "   - DB_USERNAME=votre_utilisateur"
    echo "   - DB_PASSWORD=votre_mot_de_passe"
    echo ""
    read -p "Appuyez sur Entrée après avoir configuré la base de données..."
else
    echo "✓ Le fichier .env existe déjà"
fi

echo ""
echo "🗄️  Migration de la base de données..."
php artisan migrate:fresh --force

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de la migration. Vérifiez votre configuration de base de données."
    exit 1
fi

echo ""
echo "🌱 Remplissage de la base de données avec des données de test..."
php artisan db:seed --force

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors du seeding de la base de données."
    exit 1
fi

echo ""
echo "🔗 Création du lien symbolique pour le stockage..."
php artisan storage:link

echo ""
echo "🧹 Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "======================================"
echo "✅ Installation terminée avec succès!"
echo "======================================"
echo ""
echo "Comptes de test créés:"
echo ""
echo "👑 Administrateur:"
echo "   Email: admin@dropshipping.tn"
echo "   Mot de passe: password"
echo ""
echo "🏪 Fournisseurs actifs:"
echo "   Email: supplier1@example.tn (TechStore Tunisia - 10% commission)"
echo "   Email: supplier2@example.tn (Mode Chic - 12% commission)"
echo "   Email: supplier3@example.tn (Maison & Déco - 15% commission)"
echo "   Mot de passe: password"
echo ""
echo "👤 Clients:"
echo "   Email: client1@example.tn"
echo "   Email: client2@example.tn"
echo "   Email: client3@example.tn"
echo "   Mot de passe: password"
echo ""
echo "⏳ Fournisseur en attente:"
echo "   Email: pending@example.tn"
echo "   Mot de passe: password"
echo ""
echo "Pour démarrer le serveur de développement:"
echo "   php artisan serve"
echo ""
echo "Ensuite, ouvrez votre navigateur à l'adresse:"
echo "   http://localhost:8000"
echo ""
