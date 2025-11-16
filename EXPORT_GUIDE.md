# Guide d'Export Excel

Ce guide explique comment utiliser les fonctionnalités d'export Excel disponibles dans la plateforme de dropshipping.

## 📋 Vue d'ensemble

Le système d'export permet aux fournisseurs et administrateurs d'exporter leurs données en fichiers Excel (.xlsx) pour l'analyse, la comptabilité et les rapports. Tous les exports sont générés avec des en-têtes formatés et des données structurées.

## 🎯 Exports Disponibles

### Pour les Fournisseurs

#### 1. Export des Produits

**Route:** `GET /fournisseur/produits/export`

**Filtres disponibles:**
- `status` - Statut du produit (active, inactive, draft)
- `category_id` - ID de la catégorie

**Colonnes exportées:**
- Nom du produit
- SKU
- Catégorie
- Prix (DT)
- Stock
- Statut
- Date de création
- Date d'approbation
- Nombre de ventes
- Chiffre d'affaires total
- Note moyenne

**Exemple d'utilisation:**
```php
// Export tous les produits actifs
route('supplier.products.export', ['status' => 'active'])

// Export des produits d'une catégorie spécifique
route('supplier.products.export', ['category_id' => 5])
```

#### 2. Export des Commandes

**Route:** `GET /fournisseur/commandes/export`

**Filtres disponibles:**
- `status` - Statut de la commande (pending, processing, shipped, delivered, cancelled)
- `date_from` - Date de début (format: Y-m-d)
- `date_to` - Date de fin (format: Y-m-d)

**Colonnes exportées:**
- N° Commande
- Date
- Client
- Email Client
- Produit
- SKU
- Quantité
- Prix Unitaire
- Sous-total
- Commission (%)
- Commission (DT)
- Montant Fournisseur
- Statut
- Suivi Expédition
- Date Livraison

**Exemple d'utilisation:**
```php
// Export des commandes expédiées du mois
route('supplier.orders.export', [
    'status' => 'shipped',
    'date_from' => '2025-11-01',
    'date_to' => '2025-11-30'
])
```

### Pour les Administrateurs

#### 1. Export des Commandes

**Route:** `GET /admin/commandes/export`

**Filtres disponibles:**
- `status` - Statut de la commande
- `payment_method` - Mode de paiement (card, edinar, cod)
- `date_from` - Date de début
- `date_to` - Date de fin

**Colonnes exportées:**
- N° Commande
- Date
- Client
- Email
- Téléphone
- Nb Articles
- Sous-total
- Frais Livraison
- Total
- Mode Paiement
- Statut Paiement
- Statut Commande
- Ville Livraison
- Date Confirmation
- Date Livraison

**Exemple d'utilisation:**
```php
// Export des commandes payées par carte du trimestre
route('admin.orders.export', [
    'payment_method' => 'card',
    'date_from' => '2025-10-01',
    'date_to' => '2025-12-31'
])
```

#### 2. Export des Commissions

**Route:** `GET /admin/commissions/export`

**Filtres disponibles:**
- `supplier_id` - ID du fournisseur
- `status` - Statut (pending, approved, paid, cancelled)
- `date_from` - Date de début
- `date_to` - Date de fin

**Colonnes exportées:**
- Date
- N° Commande
- Fournisseur
- Produit
- Quantité
- Prix Unitaire
- Sous-total Vente
- Taux Commission (%)
- Montant Commission
- Montant Fournisseur
- Statut
- Date Approbation
- Date Paiement

**Exemple d'utilisation:**
```php
// Export des commissions payées d'un fournisseur
route('admin.commissions.export', [
    'supplier_id' => 123,
    'status' => 'paid',
    'date_from' => '2025-01-01',
    'date_to' => '2025-12-31'
])
```

## 🎨 Format des Fichiers

### En-têtes Stylisés

Chaque type d'export a un code couleur distinct:
- **Produits (Fournisseur):** Violet (#8B5CF6)
- **Commandes (Fournisseur):** Bleu (#3B82F6)
- **Commandes (Admin):** Vert (#10B981)
- **Commissions (Admin):** Orange (#F59E0B)

### Colonnes Auto-ajustées

Les colonnes sont automatiquement redimensionnées pour un affichage optimal.

### Valeurs Formatées

- **Montants:** Formatés avec 2 décimales (ex: 1299.00)
- **Dates:** Format français dd/mm/YYYY (ex: 16/11/2025)
- **Statuts:** Traduits en français
- **Valeurs nulles:** Affichées comme "-"

## 💻 Utilisation Technique

### Dans un Contrôleur

```php
use App\Exports\SupplierOrdersExport;
use Maatwebsite\Excel\Facades\Excel;

public function export(Request $request)
{
    $filters = $request->only(['status', 'date_from', 'date_to']);

    return Excel::download(
        new SupplierOrdersExport(auth()->id(), $filters),
        'commandes_' . date('Y-m-d') . '.xlsx'
    );
}
```

### Dans une Vue Blade

```blade
<!-- Bouton d'export simple -->
<a href="{{ route('supplier.orders.export') }}" class="btn btn-primary">
    Exporter en Excel
</a>

<!-- Avec filtres -->
<form action="{{ route('supplier.orders.export') }}" method="GET">
    <select name="status">
        <option value="">Tous les statuts</option>
        <option value="pending">En attente</option>
        <option value="shipped">Expédiée</option>
    </select>

    <input type="date" name="date_from" placeholder="Date début">
    <input type="date" name="date_to" placeholder="Date fin">

    <button type="submit">Exporter</button>
</form>
```

### Avec JavaScript/AJAX

```javascript
// Export avec filtres dynamiques
function exportOrders() {
    const params = new URLSearchParams({
        status: document.getElementById('status').value,
        date_from: document.getElementById('date_from').value,
        date_to: document.getElementById('date_to').value
    });

    // Télécharger le fichier
    window.location.href = `/fournisseur/commandes/export?${params}`;
}
```

## 📊 Performances

### Limites Recommandées

- **Maximum:** ~10,000 lignes par export
- **Optimal:** 1,000-5,000 lignes
- **Temps de génération:** 1-3 secondes pour 1,000 lignes

### Optimisations

Les exports utilisent:
- **FromQuery:** Chargement efficace depuis la base de données
- **Eager Loading:** Réduction des requêtes N+1
- **Streaming:** Génération progressive du fichier

## 🔍 Dépannage

### Problème: Export vide

**Cause:** Aucune donnée ne correspond aux filtres
**Solution:** Vérifier les paramètres de filtrage

### Problème: Timeout sur gros export

**Cause:** Trop de données
**Solution:** Utiliser des filtres de date pour réduire la quantité

### Problème: Colonnes décalées

**Cause:** Données avec caractères spéciaux
**Solution:** Les exports gèrent automatiquement l'encodage UTF-8

### Problème: Erreur 500

**Cause:** Manque de mémoire
**Solution:**
1. Augmenter `memory_limit` dans php.ini
2. Utiliser des exports par période

## 📦 Package Utilisé

**maatwebsite/excel v3.1**

Documentation complète: https://docs.laravel-excel.com

## 🔐 Sécurité

### Contrôle d'Accès

- Tous les exports nécessitent une authentification
- Les fournisseurs ne peuvent exporter que leurs propres données
- Les admins ont accès à tous les exports

### Validation des Filtres

```php
$request->validate([
    'status' => 'sometimes|string|in:pending,shipped,delivered',
    'date_from' => 'sometimes|date',
    'date_to' => 'sometimes|date|after_or_equal:date_from',
]);
```

## 📝 Exemples de Cas d'Usage

### Comptabilité Mensuelle

```php
// Export des commissions du mois pour comptabilité
route('admin.commissions.export', [
    'status' => 'paid',
    'date_from' => '2025-11-01',
    'date_to' => '2025-11-30'
])
```

### Analyse des Ventes

```php
// Export des produits avec statistiques de vente
route('supplier.products.export', ['status' => 'active'])
```

### Suivi des Expéditions

```php
// Export des commandes expédiées à livrer
route('supplier.orders.export', [
    'status' => 'shipped',
    'date_from' => date('Y-m-d', strtotime('-7 days'))
])
```

### Rapport Trimestriel

```php
// Export complet des commandes du trimestre
route('admin.orders.export', [
    'date_from' => '2025-10-01',
    'date_to' => '2025-12-31'
])
```

## 🆕 Futures Améliorations

- Export en CSV (en plus d'Excel)
- Export PDF pour factures
- Exports planifiés automatiques
- Export avec graphiques intégrés
- Templates d'export personnalisables
