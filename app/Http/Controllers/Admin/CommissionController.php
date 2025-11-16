<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use App\Exports\CommissionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    /**
     * Affiche la liste des commissions
     */
    public function index(Request $request)
    {
        $query = Commission::with(['supplier', 'orderItem.order', 'orderItem.product']);

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtrer par fournisseur
        if ($request->has('supplier_id') && $request->supplier_id !== 'all') {
            $query->where('supplier_id', $request->supplier_id);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques par statut
        $totals = [
            'all' => Commission::sum('amount'),
            'pending' => Commission::where('status', 'pending')->sum('amount'),
            'approved' => Commission::where('status', 'approved')->sum('amount'),
            'paid' => Commission::where('status', 'paid')->sum('amount'),
        ];

        // Compter par statut
        $statusCounts = [
            'all' => Commission::count(),
            'pending' => Commission::where('status', 'pending')->count(),
            'approved' => Commission::where('status', 'approved')->count(),
            'paid' => Commission::where('status', 'paid')->count(),
        ];

        // Liste des fournisseurs pour le filtre
        $suppliers = User::where('role', 'supplier')->orderBy('business_name')->get();

        return view('admin.commissions.index', compact(
            'commissions',
            'totals',
            'statusCounts',
            'suppliers'
        ));
    }

    /**
     * Affiche les commissions d'un fournisseur spécifique
     */
    public function supplier(User $user)
    {
        // Vérifier que c'est un fournisseur
        if ($user->role !== 'supplier') {
            abort(404);
        }

        $commissions = Commission::where('supplier_id', $user->id)
            ->with(['orderItem.order', 'orderItem.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistiques du fournisseur
        $stats = [
            'total_commissions' => Commission::where('supplier_id', $user->id)->sum('amount'),
            'pending' => Commission::where('supplier_id', $user->id)->where('status', 'pending')->sum('amount'),
            'approved' => Commission::where('supplier_id', $user->id)->where('status', 'approved')->sum('amount'),
            'paid' => Commission::where('supplier_id', $user->id)->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.commissions.supplier', compact('user', 'commissions', 'stats'));
    }

    /**
     * Marque une commission comme payée
     */
    public function markAsPaid(Commission $commission)
    {
        // Vérifier que la commission est approuvée
        if ($commission->status !== 'approved') {
            return back()->with('error', 'Seules les commissions approuvées peuvent être marquées comme payées.');
        }

        DB::beginTransaction();
        try {
            $commission->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // TODO: Enregistrer la transaction de paiement
            // TODO: Notifier le fournisseur

            DB::commit();

            return back()->with('success', 'Commission marquée comme payée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Génère un rapport de commissions
     */
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Commissions par fournisseur pour la période
        $commissionsBySupplier = User::where('role', 'supplier')
            ->withSum(['commissions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', '!=', 'cancelled');
            }], 'amount')
            ->having('commissions_sum_amount', '>', 0)
            ->orderBy('commissions_sum_amount', 'desc')
            ->get();

        // Commissions par jour
        $commissionsByDay = Commission::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Commissions par statut
        $commissionsByStatus = Commission::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.commissions.report', compact(
            'commissionsBySupplier',
            'commissionsByDay',
            'commissionsByStatus',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export des commissions en Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['supplier_id', 'status', 'date_from', 'date_to']);

        return Excel::download(
            new CommissionsExport($filters),
            'commissions_' . date('Y-m-d') . '.xlsx'
        );
    }
}
