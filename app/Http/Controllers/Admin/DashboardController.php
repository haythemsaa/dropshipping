<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur
     */
    public function index()
    {
        // Statistiques globales
        $stats = [
            'total_suppliers' => User::where('role', 'supplier')->count(),
            'pending_suppliers' => User::where('role', 'supplier')->where('status', 'pending')->count(),
            'active_suppliers' => User::where('role', 'supplier')->where('status', 'active')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::whereNull('approved_at')->count(),
            'active_products' => Product::where('status', 'active')->whereNotNull('approved_at')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'total_commissions' => Commission::where('status', '!=', 'cancelled')->sum('amount'),
            'pending_commissions' => Commission::where('status', 'pending')->sum('amount'),
        ];

        // Fournisseurs en attente de validation
        $pendingSuppliers = User::where('role', 'supplier')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Produits en attente d'approbation
        $pendingProducts = Product::with(['supplier', 'category'])
            ->whereNull('approved_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Commandes récentes
        $recentOrders = Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statistiques des 7 derniers jours
        $last7DaysStats = Order::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingSuppliers',
            'pendingProducts',
            'recentOrders',
            'last7DaysStats'
        ));
    }

    /**
     * Affiche les statistiques détaillées
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', '30days');

        // Définir la période
        $startDate = match ($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30),
        };

        // Ventes par jour
        $salesByDay = Order::where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top fournisseurs
        $topSuppliers = User::where('role', 'supplier')
            ->withCount('orderItems')
            ->withSum('orderItems', 'subtotal')
            ->orderBy('order_items_sum_subtotal', 'desc')
            ->take(10)
            ->get();

        // Top produits
        $topProducts = Product::with(['supplier', 'category'])
            ->orderBy('sales_count', 'desc')
            ->take(10)
            ->get();

        // Revenus par catégorie
        $revenueByCategory = OrderItem::where('order_items.created_at', '>=', $startDate)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(order_items.id) as sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Méthodes de paiement
        $paymentMethods = Order::where('created_at', '>=', $startDate)
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->select(
                'payments.payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(payments.amount) as total')
            )
            ->groupBy('payments.payment_method')
            ->get();

        return view('admin.statistics', compact(
            'salesByDay',
            'topSuppliers',
            'topProducts',
            'revenueByCategory',
            'paymentMethods',
            'period'
        ));
    }
}
