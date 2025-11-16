# Guide d'Import CSV/Excel de Produits

## 📊 Vue d'Ensemble

Le système d'import permet aux fournisseurs d'ajouter plusieurs produits à la fois en utilisant un fichier CSV ou Excel. Cette fonctionnalité accélère considérablement l'ajout de produits en masse.

## 🚀 Fonctionnalités

- **Import en masse** jusqu'à 1000 produits à la fois
- **Template pré-formaté** avec exemples
- **Validation automatique** des données
- **Gestion des erreurs** avec rapport détaillé
- **Support multiple formats** : CSV, XLSX, XLS
- **Traitement par lots** pour optimiser les performances
- **SKU auto-généré** si non fourni
- **Slug unique** généré automatiquement

## 📋 Format du Fichier

### Colonnes Requises

| Colonne | Obligatoire | Type | Description | Exemple |
|---------|-------------|------|-------------|---------|
| `nom` | ✅ Oui | Texte | Nom du produit | Smartphone Samsung Galaxy A54 |
| `prix` | ✅ Oui | Nombre | Prix en dinars (sans symbole) | 1299.00 |
| `description` | Non | Texte | Description détaillée | Smartphone 5G avec écran AMOLED... |
| `categorie` | Non | Texte | Nom de la catégorie | Électronique |
| `stock` | Non | Nombre | Quantité en stock | 25 |
| `sku` | Non | Texte | Code produit unique | SAM-A54-128-BLK |

### Règles de Validation

```
nom:
  - Obligatoire
  - Maximum 255 caractères
  - Sera utilisé pour générer le slug

prix:
  - Obligatoire
  - Nombre positif
  - Décimales autorisées (ex: 125.50)

description:
  - Optionnel
  - Texte libre
  - Recommandé pour SEO

categorie:
  - Optionnel
  - Doit correspondre à une catégorie existante
  - Recherche partielle (ex: "Électro" trouvera "Électronique")
  - Si vide ou invalide → catégorie "Autres"

stock:
  - Optionnel
  - Nombre entier positif
  - Si vide → 0

sku:
  - Optionnel
  - Unique dans la base
  - Si vide → généré automatiquement (SKU-XXXXXXXX)
  - Si doublon → ajout d'un timestamp
```

## 🎯 Utilisation

### Étape 1: Télécharger le Template

1. Se connecter à l'espace fournisseur
2. Aller dans **Produits** → **Importer des produits**
3. Cliquer sur **"Télécharger le template Excel"**
4. Un fichier `template_produits_YYYY-MM-DD.xlsx` sera téléchargé

### Étape 2: Remplir le Fichier

1. Ouvrir le fichier avec :
   - Microsoft Excel
   - LibreOffice Calc
   - Google Sheets
   - Numbers (Mac)

2. **Ne pas modifier** les en-têtes de colonnes

3. Remplir les données en suivant les exemples fournis

4. Sauvegarder le fichier en :
   - `.xlsx` (recommandé)
   - `.xls`
   - `.csv` (UTF-8)

### Étape 3: Importer le Fichier

1. Retourner sur la page d'import
2. Cliquer sur **"Choisir un fichier"** ou glisser-déposer
3. Sélectionner votre fichier
4. Cliquer sur **"Lancer l'import"**
5. Attendre le traitement

### Étape 4: Vérifier les Résultats

Après l'import, un message s'affichera :

#### ✅ Import Réussi
```
Import terminé !
250 produits importés avec succès.
Les produits sont en attente d'approbation.
```

#### ⚠️ Import Partiel
```
Import terminé !
230 produits importés avec succès.
20 produits ont échoué.
```

#### ❌ Erreurs

Les erreurs sont affichées avec le numéro de ligne :
```
Ligne 15: Le nom du produit est obligatoire
Ligne 28: Le prix doit être un nombre
Ligne 42: Le prix doit être positif
```

## 💡 Exemples

### Exemple de Ligne Valide

```csv
nom,prix,description,categorie,stock,sku
"Smartphone Samsung Galaxy A54",1299.00,"Smartphone 5G avec écran AMOLED 6.4"", 128GB de stockage, appareil photo 50MP. Couleur: Noir.",Électronique,25,SAM-A54-128-BLK
```

### Exemple avec Minimum Requis

```csv
nom,prix,description,categorie,stock,sku
"T-Shirt Homme",45.00,,,100,
```

Ce produit sera créé avec :
- Nom : T-Shirt Homme
- Prix : 45.00 DT
- Description : (vide)
- Catégorie : Autres (par défaut)
- Stock : 100
- SKU : SKU-ABCD1234 (auto-généré)

### Exemple CSV Complet

```csv
nom,prix,description,categorie,stock,sku
"Smartphone Samsung Galaxy A54",1299.00,"Smartphone 5G avec écran AMOLED",Électronique,25,SAM-A54-128
"T-Shirt Homme Coton",45.00,"T-shirt en coton 100%",Mode Homme,100,TSHIRT-M-001
"Canapé 3 Places",2499.00,"Canapé avec revêtement tissu gris",Maison & Décor,5,CANAPE-3P-GREY
```

## 🔧 Traitement Technique

### Processus d'Import

```
1. Validation du fichier (format, taille max 10 MB)
2. Lecture par lots de 100 lignes (chunking)
3. Pour chaque ligne:
   a. Validation des données
   b. Recherche de la catégorie
   c. Génération SKU/Slug si nécessaire
   d. Création du produit
4. Insertion par lots de 100 (batch insert)
5. Génération du rapport
```

### Performance

- **Vitesse** : ~1000 produits en 10-15 secondes
- **Mémoire** : Optimisé avec chunking
- **Timeout** : Pas de limite (traitement par lots)

### Produits Créés

Les produits importés auront automatiquement :

```php
'supplier_id' => ID du fournisseur connecté
'status' => 'inactive' // En attente d'activation
'approved_at' => null // Nécessite approbation admin
'stock_status' => 'in_stock' ou 'out_of_stock' (selon le stock)
'slug' => 'nom-du-produit' // Auto-généré unique
```

## ⚙️ Configuration

### Limites

```php
// Taille maximale du fichier
max_file_size: 10 MB

// Nombre maximum de produits
max_products: 1000 par import

// Taille des lots
batch_size: 100 produits
chunk_size: 100 lignes
```

### Formats Acceptés

- **CSV** : Encodage UTF-8, délimiteur `,` ou `;`
- **XLSX** : Excel 2007+
- **XLS** : Excel 97-2003

## 🐛 Troubleshooting

### Problème : Caractères Accentués Incorrects

**Solution :** Enregistrer le CSV en UTF-8
- Excel : Fichier → Enregistrer sous → CSV UTF-8
- LibreOffice : Cocher "UTF-8" lors de l'export
- Google Sheets : Télécharger comme CSV

### Problème : Import Trop Lent

**Solutions :**
1. Réduire le nombre de produits par fichier (max 500 recommandé)
2. Simplifier les descriptions (moins de texte)
3. Importer en plusieurs fois

### Problème : "Le prix doit être un nombre"

**Causes :**
- Virgule au lieu du point : `125,50` → `125.50`
- Symbole de devise : `125 DT` → `125.00`
- Espaces : `125 .50` → `125.50`

**Solution :** Utiliser le format `125.50` (point comme décimale)

### Problème : Catégorie Non Trouvée

**Solution :**
1. Vérifier l'orthographe exacte
2. Utiliser le nom complet (ex: "Électronique" pas "Electro")
3. Ou laisser vide pour "Autres"

### Problème : SKU en Doublon

**Comportement :**
- Si SKU existe déjà → ajout d'un timestamp automatique
- Ex: `PROD-001` devient `PROD-001-1234567890`

**Recommandation :** Utiliser des SKU uniques dès le départ

## 📊 Statistiques d'Import

Après chaque import, les statistiques suivantes sont affichées :

```php
[
    'imported' => 250,  // Produits créés
    'skipped' => 5,     // Lignes vides ignorées
    'failed' => 20,     // Erreurs de validation
]
```

## 🔐 Sécurité

### Permissions

- Seuls les fournisseurs approuvés peuvent importer
- Les produits sont liés automatiquement au fournisseur connecté
- Impossible d'importer pour un autre fournisseur

### Validation

- Tous les produits sont validés avant insertion
- Les données malveillantes sont nettoyées
- Protection contre les injections SQL

### Approbation

- Tous les produits importés nécessitent une approbation admin
- Status initial : `inactive`
- Approbation via le panneau admin

## 📈 Améliorations Futures

### Version 1.1
- [ ] Import d'images via URL
- [ ] Support de colonnes additionnelles (poids, dimensions)
- [ ] Import de variantes produits

### Version 1.2
- [ ] Mise à jour des produits existants (update mode)
- [ ] Import incrémental
- [ ] Historique des imports

### Version 2.0
- [ ] Import depuis API externe
- [ ] Synchronisation automatique
- [ ] Import planifié (cron)

## 💻 Code d'Exemple

### Controller Method

```php
public function import(Request $request)
{
    $import = new ProductsImport(auth()->id());
    Excel::import($import, $request->file('file'));

    $stats = $import->getStats();

    return redirect()
        ->route('supplier.products.index')
        ->with('success', "{$stats['imported']} produits importés");
}
```

### Import Class

```php
class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Product([
            'supplier_id' => $this->supplierId,
            'name' => $row['nom'],
            'price' => (float)$row['prix'],
            // ... autres champs
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
        ];
    }
}
```

## 📞 Support

### En Cas de Problème

1. Vérifier les [erreurs courantes](#troubleshooting)
2. Consulter les exemples fournis dans le template
3. Réduire le nombre de lignes et réessayer
4. Contacter le support avec :
   - Le fichier problématique
   - Le message d'erreur exact
   - Le nombre de lignes

### Resources

- **Documentation Laravel Excel :** https://docs.laravel-excel.com
- **Template de test :** `/fournisseur/produits/template`
- **Catégories disponibles :** Affichées sur la page d'import

---

**Version :** 1.0
**Dernière mise à jour :** 2025-01-XX
**Package utilisé :** maatwebsite/excel v3.1
