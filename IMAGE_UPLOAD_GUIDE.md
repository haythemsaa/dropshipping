# Guide d'Upload d'Images - Plateforme Dropshipping Tunisia

## 📸 Fonctionnalité d'Upload d'Images

### Fonctionnalités implémentées

✅ **Service ImageUploadService complet** :
- Validation stricte des images (formats, taille, dimensions)
- Redimensionnement automatique (max 1920x1920px)
- Création de thumbnails (300x300px)
- Optimisation de la qualité (85% pour JPEG/WEBP)
- Préservation de la transparence (PNG, GIF)
- Support JPEG, PNG, GIF, WEBP

✅ **Contrôleur SupplierProductController** :
- `uploadImage()` - Upload d'une image pour un produit
- `deleteImage()` - Suppression d'image avec son thumbnail
- `setPrimaryImage()` - Définir l'image principale

✅ **Routes configurées** :
```php
POST   /fournisseur/produits/{product}/images          -> uploadImage
DELETE /fournisseur/produits/images/{image}            -> deleteImage
POST   /fournisseur/produits/images/{image}/set-primary -> setPrimaryImage
```

✅ **Vues disponibles** :
- `supplier/products/edit.blade.php` - Formulaire d'upload d'images

## 🚀 Utilisation

### 1. Configuration du stockage

Assurez-vous que le lien symbolique est créé :

```bash
php artisan storage:link
```

Cela crée un lien entre `public/storage` et `storage/app/public`.

### 2. Vérifier les permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. Upload depuis l'interface fournisseur

1. Se connecter en tant que fournisseur
2. Aller dans "Mes produits"
3. Cliquer sur "Éditer" pour un produit
4. Utiliser le formulaire d'upload d'images
5. La première image devient automatiquement l'image principale
6. Gérer les images (supprimer, changer la principale)

## 📝 Spécifications Techniques

### Validation des images

| Critère | Valeur |
|---------|--------|
| **Formats acceptés** | JPEG, PNG, GIF, WEBP |
| **Taille maximale** | 5 Mo |
| **Dimensions minimales** | 200x200 pixels |
| **Dimensions recommandées** | 800x800 pixels ou plus |

### Traitement automatique

| Opération | Description |
|-----------|-------------|
| **Redimensionnement** | Images >1920px redimensionnées |
| **Thumbnail** | Créé automatiquement (300x300px) |
| **Optimisation** | JPEG/WEBP à 85% qualité |
| **Organisation** | `products/YYYY/MM/filename.ext` |
| **Transparence** | Préservée pour PNG et GIF |

### Structure des fichiers

```
storage/app/public/products/
├── 2025/
│   ├── 01/
│   │   ├── abc123...xyz.jpg          (image originale)
│   │   ├── thumb_abc123...xyz.jpg    (thumbnail)
│   │   ├── def456...uvw.png
│   │   └── thumb_def456...uvw.png
│   └── 02/
│       └── ...
```

## 🔧 Amélioration avec le Service

### Option A : Utiliser le service (recommandé)

Modifier `SupplierProductController::uploadImage()` pour utiliser le service :

```php
use App\Services\ImageUploadService;

public function uploadImage(Request $request, Product $product)
{
    if ($product->supplier_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
    ]);

    DB::beginTransaction();
    try {
        // Utiliser le service d'upload
        $imageService = new ImageUploadService();
        $result = $imageService->uploadProductImage($request->file('image'));

        // Déterminer si c'est la première image
        $isPrimary = $product->images()->count() === 0;

        if (!$isPrimary && $request->has('is_primary')) {
            $product->images()->update(['is_primary' => false]);
            $isPrimary = true;
        }

        // Créer l'enregistrement
        $product->images()->create([
            'image_path' => $result['path'],
            'is_primary' => $isPrimary,
            'order' => $product->images()->max('order') + 1,
        ]);

        DB::commit();

        return back()->with('success', 'Image optimisée et ajoutée avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
```

### Option B : Garder l'approche simple (actuelle)

L'approche actuelle fonctionne bien pour un MVP. Le service est disponible pour une optimisation future.

## 🎨 Améliorations Futures (Optionnel)

### 1. Installation d'Intervention Image (recommandé)

Pour des fonctionnalités avancées :

```bash
composer require intervention/image
```

Puis configurer dans `config/app.php` :

```php
'providers' => [
    // ...
    Intervention\Image\ImageServiceProvider::class,
],

'aliases' => [
    // ...
    'Image' => Intervention\Image\Facades\Image::class,
],
```

### 2. Conversion automatique en WebP

Ajouter au service :

```php
protected function convertToWebP($sourcePath)
{
    $image = imagecreatefromstring(file_get_contents($sourcePath));
    ob_start();
    imagewebp($image, null, 85);
    $webpData = ob_get_clean();
    imagedestroy($image);
    return $webpData;
}
```

### 3. Support des images multiples simultanées

Modifier le formulaire pour accepter plusieurs fichiers :

```html
<input type="file" name="images[]" multiple accept="image/*">
```

Puis dans le contrôleur :

```php
foreach ($request->file('images') as $image) {
    // Upload chaque image
}
```

### 4. Lazy Loading et CDN

Pour la production :
- Utiliser un CDN (Cloudflare, AWS CloudFront)
- Activer lazy loading sur les images frontend
- Servir les WebP avec fallback JPEG

## 🐛 Dépannage

### Erreur : "The file does not exist at path..."

Solution :
```bash
php artisan storage:link
```

### Erreur : "Permission denied"

Solution :
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

### Images trop volumineuses

Vérifier `php.ini` :
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### GD non disponible

Installer GD :
```bash
sudo apt-get install php-gd
# ou
sudo yum install php-gd
```

## 📊 Statistiques d'Upload

Le service enregistre automatiquement :
- Chemin du fichier (`image_path`)
- Taille du fichier (`size` retournée)
- Ordre d'affichage (`order`)
- Image principale (`is_primary`)

## 🔐 Sécurité

✅ **Mesures implémentées** :
- Validation stricte du type MIME
- Vérification avec `getimagesize()`
- Limite de taille (5 Mo)
- Noms de fichiers aléatoires (40 caractères)
- Vérification de propriété (supplier_id)
- Organisation par date (évite conflits)

## 📱 Responsive

Les thumbnails (300x300) sont parfaits pour :
- Listes de produits
- Cartes produits
- Aperçus mobiles

Les images originales (max 1920px) pour :
- Pages produit détaillées
- Zoom/Lightbox
- Impression qualité

---

**Dernière mise à jour :** 2025-01-16
**Version :** 1.0
