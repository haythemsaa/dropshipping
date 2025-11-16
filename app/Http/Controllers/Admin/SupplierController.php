<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Affiche la liste des fournisseurs
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'supplier')
            ->withCount('products')
            ->withCount('orderItems');

        // Filtrer par statut
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('business_name', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Compter par statut
        $statusCounts = [
            'all' => User::where('role', 'supplier')->count(),
            'pending' => User::where('role', 'supplier')->where('status', 'pending')->count(),
            'active' => User::where('role', 'supplier')->where('status', 'active')->count(),
            'suspended' => User::where('role', 'supplier')->where('status', 'suspended')->count(),
            'rejected' => User::where('role', 'supplier')->where('status', 'rejected')->count(),
        ];

        return view('admin.suppliers.index', compact('suppliers', 'statusCounts'));
    }

    /**
     * Affiche les détails d'un fournisseur
     */
    public function show(User $user)
    {
        // Vérifier que c'est bien un fournisseur
        if ($user->role !== 'supplier') {
            abort(404);
        }

        $user->load(['products' => function ($query) {
            $query->withCount('orderItems')->orderBy('created_at', 'desc');
        }]);

        // Statistiques du fournisseur
        $stats = [
            'total_products' => $user->products()->count(),
            'active_products' => $user->products()->where('status', 'active')->count(),
            'total_sales' => $user->getTotalSales(),
            'total_orders' => $user->orderItems()->count(),
            'pending_orders' => $user->orderItems()->where('status', 'pending')->count(),
            'completed_orders' => $user->orderItems()->where('status', 'delivered')->count(),
        ];

        // Commissions du fournisseur
        $commissions = $user->commissions()
            ->with('orderItem.order')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.suppliers.show', compact('user', 'stats', 'commissions'));
    }

    /**
     * Approuve un fournisseur
     */
    public function approve(User $user)
    {
        // Vérifier que c'est un fournisseur en attente
        if ($user->role !== 'supplier' || $user->status !== 'pending') {
            return back()->with('error', 'Ce fournisseur ne peut pas être approuvé.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // TODO: Envoyer email de confirmation au fournisseur

            DB::commit();

            return redirect()->route('admin.suppliers.show', $user)
                ->with('success', 'Fournisseur approuvé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'approbation.');
        }
    }

    /**
     * Rejette un fournisseur
     */
    public function reject(User $user)
    {
        // Vérifier que c'est un fournisseur en attente
        if ($user->role !== 'supplier' || $user->status !== 'pending') {
            return back()->with('error', 'Ce fournisseur ne peut pas être rejeté.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'status' => 'rejected',
            ]);

            // TODO: Envoyer email de notification au fournisseur

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Fournisseur rejeté.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du rejet.');
        }
    }

    /**
     * Suspend un fournisseur
     */
    public function suspend(User $user)
    {
        // Vérifier que c'est un fournisseur actif
        if ($user->role !== 'supplier' || $user->status !== 'active') {
            return back()->with('error', 'Ce fournisseur ne peut pas être suspendu.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'status' => 'suspended',
            ]);

            // Désactiver tous les produits du fournisseur
            $user->products()->update(['status' => 'inactive']);

            // TODO: Envoyer email de notification au fournisseur

            DB::commit();

            return back()->with('success', 'Fournisseur suspendu. Tous ses produits ont été désactivés.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suspension.');
        }
    }

    /**
     * Réactive un fournisseur suspendu
     */
    public function activate(User $user)
    {
        // Vérifier que c'est un fournisseur suspendu
        if ($user->role !== 'supplier' || $user->status !== 'suspended') {
            return back()->with('error', 'Ce fournisseur ne peut pas être activé.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'status' => 'active',
            ]);

            // TODO: Envoyer email de notification au fournisseur

            DB::commit();

            return back()->with('success', 'Fournisseur réactivé. Les produits restent désactivés et devront être réactivés manuellement.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la réactivation.');
        }
    }

    /**
     * Met à jour le taux de commission d'un fournisseur
     */
    public function updateCommission(Request $request, User $user)
    {
        // Vérifier que c'est un fournisseur
        if ($user->role !== 'supplier') {
            abort(404);
        }

        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'commission_rate' => $validated['commission_rate'],
            ]);

            DB::commit();

            return back()->with('success', 'Taux de commission mis à jour à ' . $validated['commission_rate'] . '%');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }
}
