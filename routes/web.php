<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;
use App\Http\Controllers\Supplier\ProductController as SupplierProductController;
use App\Http\Controllers\Supplier\OrderController as SupplierOrderController;
use App\Http\Controllers\Supplier\ShipmentController as SupplierShipmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentWebhookController;

/*
|--------------------------------------------------------------------------
| Routes Publiques (Frontend)
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalogue produits
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produits/recherche', [ProductController::class, 'search'])->name('products.search');
Route::get('/produits/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');
Route::get('/categorie/{category:slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/panier', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Routes Authentifiées (Clients)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Profil utilisateur (fourni par Laravel Breeze)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Adresses
    Route::get('/profil/adresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profil/adresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::patch('/profil/adresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profil/adresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');

    // Commandes (accessible à tous les utilisateurs authentifiés)
    Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Processus de commande (clients uniquement)
    Route::middleware('client')->group(function () {
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
        Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/commande/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
        Route::post('/commande/{order}/annuler', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Avis produits (clients seulement)
        Route::get('/produit/{product}/avis/creer', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/produit/{product}/avis', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/avis/{review}/modifier', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::patch('/avis/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/avis/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Marquer un avis comme utile (tous les utilisateurs authentifiés)
    Route::post('/avis/{review}/utile', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');
});

/*
|--------------------------------------------------------------------------
| Routes Fournisseurs
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'active', 'supplier'])->prefix('fournisseur')->name('supplier.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');

    // Gestion des produits
    Route::get('/produits', [SupplierProductController::class, 'index'])->name('products.index');
    Route::get('/produits/create', [SupplierProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [SupplierProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/edit', [SupplierProductController::class, 'edit'])->name('products.edit');
    Route::patch('/produits/{product}', [SupplierProductController::class, 'update'])->name('products.update');
    Route::delete('/produits/{product}', [SupplierProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/produits/{product}/toggle-status', [SupplierProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Images de produits
    Route::post('/produits/{product}/images', [SupplierProductController::class, 'uploadImage'])->name('products.images.upload');
    Route::delete('/produits/images/{image}', [SupplierProductController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('/produits/images/{image}/set-primary', [SupplierProductController::class, 'setPrimaryImage'])->name('products.images.set-primary');

    // Import de produits
    Route::get('/produits/import', [SupplierProductController::class, 'showImport'])->name('products.import');
    Route::post('/produits/import', [SupplierProductController::class, 'import'])->name('products.import.process');
    Route::get('/produits/template', [SupplierProductController::class, 'downloadTemplate'])->name('products.template');

    // Export de produits
    Route::get('/produits/export', [SupplierProductController::class, 'export'])->name('products.export');

    // Gestion des commandes
    Route::get('/commandes', [SupplierOrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [SupplierOrderController::class, 'show'])->name('orders.show');
    Route::post('/commandes/{orderItem}/accepter', [SupplierOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/commandes/{orderItem}/refuser', [SupplierOrderController::class, 'reject'])->name('orders.reject');

    // Export de commandes
    Route::get('/commandes/export', [SupplierOrderController::class, 'export'])->name('orders.export');

    // Gestion des expéditions
    Route::get('/expeditions', [SupplierShipmentController::class, 'index'])->name('shipments.index');
    Route::post('/expeditions/{orderItem}', [SupplierShipmentController::class, 'create'])->name('shipments.create');
    Route::patch('/expeditions/{shipment}', [SupplierShipmentController::class, 'update'])->name('shipments.update');
    Route::post('/expeditions/{shipment}/tracking', [SupplierShipmentController::class, 'updateTracking'])->name('shipments.tracking.update');

    // Statistiques et rapports
    Route::get('/statistiques', [SupplierDashboardController::class, 'statistics'])->name('statistics');
    Route::get('/commissions', [SupplierDashboardController::class, 'commissions'])->name('commissions');
});

/*
|--------------------------------------------------------------------------
| Routes Administrateur
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistiques', [AdminDashboardController::class, 'statistics'])->name('statistics');

    // Gestion des fournisseurs
    Route::get('/fournisseurs', [AdminSupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/fournisseurs/{user}', [AdminSupplierController::class, 'show'])->name('suppliers.show');
    Route::post('/fournisseurs/{user}/approuver', [AdminSupplierController::class, 'approve'])->name('suppliers.approve');
    Route::post('/fournisseurs/{user}/rejeter', [AdminSupplierController::class, 'reject'])->name('suppliers.reject');
    Route::post('/fournisseurs/{user}/suspendre', [AdminSupplierController::class, 'suspend'])->name('suppliers.suspend');
    Route::post('/fournisseurs/{user}/activer', [AdminSupplierController::class, 'activate'])->name('suppliers.activate');
    Route::patch('/fournisseurs/{user}/commission', [AdminSupplierController::class, 'updateCommission'])->name('suppliers.update-commission');

    // Gestion des catégories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Modération des produits
    Route::get('/produits', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/produits/{product}', [AdminProductController::class, 'show'])->name('products.show');
    Route::post('/produits/{product}/approuver', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('/produits/{product}/rejeter', [AdminProductController::class, 'reject'])->name('products.reject');
    Route::delete('/produits/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Gestion des commandes
    Route::get('/commandes', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/commandes/{order}/annuler', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/commandes/{order}/statut', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/commandes/export', [AdminOrderController::class, 'export'])->name('orders.export');

    // Gestion des commissions
    Route::get('/commissions', [AdminCommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/fournisseur/{user}', [AdminCommissionController::class, 'supplier'])->name('commissions.supplier');
    Route::post('/commissions/{commission}/payer', [AdminCommissionController::class, 'markAsPaid'])->name('commissions.mark-paid');
    Route::get('/commissions/rapport', [AdminCommissionController::class, 'report'])->name('commissions.report');
    Route::get('/commissions/export', [AdminCommissionController::class, 'export'])->name('commissions.export');

    // Modération des avis
    Route::get('/avis', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/avis/{review}/approuver', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/avis/{review}/rejeter', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/avis/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/avis/approuver-masse', [AdminReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
    Route::post('/avis/supprimer-masse', [AdminReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
});

/*
|--------------------------------------------------------------------------
| Page compte en attente
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/compte-en-attente', function () {
        if (auth()->user()->isActive()) {
            return redirect()->route('home');
        }
        return view('account.pending');
    })->name('account.pending');
});

/*
|--------------------------------------------------------------------------
| Payment Webhooks (Sans Auth)
|--------------------------------------------------------------------------
*/

// Webhooks des passerelles de paiement
Route::post('/payment/webhook/edinar', [PaymentWebhookController::class, 'edinarWebhook'])
    ->name('payment.callback.edinar.notify');
Route::post('/payment/webhook/clictopay', [PaymentWebhookController::class, 'clictopayWebhook'])
    ->name('payment.callback.clictopay.webhook');
Route::post('/payment/webhook/konnect', [PaymentWebhookController::class, 'konnectWebhook'])
    ->name('payment.callback.konnect.webhook');

// Pages de retour après paiement
Route::get('/payment/return/{gateway}', [PaymentWebhookController::class, 'paymentReturn'])
    ->name('payment.callback.edinar.return')
    ->name('payment.callback.clictopay.return')
    ->name('payment.callback.konnect.success');

Route::get('/payment/cancel/{gateway}', [PaymentWebhookController::class, 'paymentCancel'])
    ->name('payment.callback.edinar.cancel')
    ->name('payment.callback.clictopay.cancel')
    ->name('payment.callback.konnect.fail');

require __DIR__.'/auth.php';
