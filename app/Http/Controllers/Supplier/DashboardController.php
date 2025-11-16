<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du fournisseur
     */
    public function index()
    {
        $supplier = auth()->user();

        // Statistiques globales
        $stats = [
            'total_products' => $supplier->products()->count(),
            'active_products' => $supplier->products()->where('status', 'active')->count(),
            'pending_products' => $supplier->products()->whereNull('approved_at')->count(),
            'total_orders' => $supplier->orderItems()->count(),
            'pending_orders' => $supplier->orderItems()->where('status', 'pending')->count(),
            'completed_orders' => $supplier->orderItems()->where('status', 'delivered')->count(),
            'total_sales' => $supplier->getTotalSales(),
            'total_commissions' => $supplier->getTotalCommissionsEarned(),
        ];

        // Commandes récentes (derniers order items)
        $recentOrders = OrderItem::where('supplier_id', $supplier->id)
            ->with(['order.user', 'product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Produits populaires
        $popularProducts = $supplier->products()
            ->where('status', 'active')
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();

        // Produits en rupture de stock
        $outOfStockProducts = $supplier->products()
            ->where('stock_quantity', '<=', 0)
            ->orWhere('status', 'out_of_stock')
            ->take(5)
            ->get();

        return view('supplier.dashboard', compact(
            'stats',
            'recentOrders',
            'popularProducts',
            'outOfStockProducts'
        ));
    }

    /**
     * Affiche les statistiques détaillées
     */
    public function statistics(Request $request)
    {
        $supplier = auth()->user();

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
        $salesByDay = OrderItem::where('supplier_id', $supplier->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Produits les plus vendus
        $topProducts = Product::where('supplier_id', $supplier->id)
            ->where('created_at', '>=', $startDate)
            ->orderBy('sales_count', 'desc')
            ->take(10)
            ->get();

        // Revenus par catégorie
        $revenueByCategory = OrderItem::where('supplier_id', $supplier->id)
            ->where('order_items.created_at', '>=', $startDate)
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

        return view('supplier.statistics', compact(
            'salesByDay',
            'topProducts',
            'revenueByCategory',
            'period'
        ));
    }

    /**
     * Affiche les commissions
     */
    public function commissions(Request $request)
    {
        $supplier = auth()->user();

        $status = $request->get('status', 'all');

        $query = Commission::where('supplier_id', $supplier->id)
            ->with(['orderItem.order.user', 'orderItem.product']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totaux par statut
        $totals = [
            'pending' => Commission::where('supplier_id', $supplier->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'approved' => Commission::where('supplier_id', $supplier->id)
                ->where('status', 'approved')
                ->sum('amount'),
            'paid' => Commission::where('supplier_id', $supplier->id)
                ->where('status', 'paid')
                ->sum('amount'),
            'all' => Commission::where('supplier_id', $supplier->id)
                ->sum('amount'),
        ];

        return view('supplier.commissions', compact('commissions', 'totals', 'status'));
    }
}
