@extends('layouts.dashboard')

@section('title', 'Mes commissions - Fournisseur')

@section('header', 'Mes commissions')

@section('sidebar')
    <a href="{{ route('supplier.dashboard') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Tableau de bord
    </a>
    <a href="{{ route('supplier.products.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Mes produits
    </a>
    <a href="{{ route('supplier.orders.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commandes
    </a>
    <a href="{{ route('supplier.shipments.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Expéditions
    </a>
    <a href="{{ route('supplier.commissions') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
        Commissions
    </a>
    <a href="{{ route('supplier.statistics') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Statistiques
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-600">Suivez vos commissions et revenus</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Revenu total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">DT</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Commissions prélevées</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_commission'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">DT</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Votre part nette</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['net_revenue'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">DT</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Taux commission</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['commission_rate'], 1) }}%</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Breakdown by Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-700">En attente</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                    {{ $stats['pending_count'] }} commande(s)
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_amount'], 2) }} DT</p>
            <p class="text-xs text-gray-500 mt-1">Montant net à recevoir</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-700">Approuvées</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ $stats['approved_count'] }} commande(s)
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['approved_amount'], 2) }} DT</p>
            <p class="text-xs text-gray-500 mt-1">En cours de traitement</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-700">Payées</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ $stats['paid_count'] }} commande(s)
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['paid_amount'], 2) }} DT</p>
            <p class="text-xs text-gray-500 mt-1">Déjà versées</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('supplier.commissions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" id="status"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvées</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payées</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Commissions Table -->
    @if($commissions->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total vente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Votre part</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($commissions as $commission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('supplier.orders.show', $commission->orderItem->order) }}"
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    #{{ $commission->orderItem->order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $commission->orderItem->product->name }}</div>
                                <div class="text-sm text-gray-500">Qté: {{ $commission->orderItem->quantity }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($commission->orderItem->subtotal, 2) }} DT</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-red-600">
                                    -{{ number_format($commission->amount, 2) }} DT
                                </div>
                                <div class="text-xs text-gray-500">({{ number_format($commission->commission_rate, 1) }}%)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-green-600">{{ number_format($commission->supplier_amount, 2) }} DT</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($commission->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($commission->status === 'approved') bg-blue-100 text-blue-800
                                    @elseif($commission->status === 'paid') bg-green-100 text-green-800
                                    @endif">
                                    @if($commission->status === 'pending') En attente
                                    @elseif($commission->status === 'approved') Approuvée
                                    @elseif($commission->status === 'paid') Payée
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $commission->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm font-medium text-gray-900">Total page :</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ number_format($commissions->sum(fn($c) => $c->orderItem->subtotal), 2) }} DT
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-red-600">
                            -{{ number_format($commissions->sum('amount'), 2) }} DT
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600">
                            {{ number_format($commissions->sum('supplier_amount'), 2) }} DT
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $commissions->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune commission trouvée</h3>
            <p class="mt-2 text-sm text-gray-500">Les commissions apparaîtront ici une fois vos produits vendus.</p>
        </div>
    @endif

    <!-- Information Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note :</strong> Les commissions sont calculées automatiquement à chaque vente selon votre taux actuel de {{ number_format($stats['commission_rate'], 1) }}%.
                    Les paiements sont traités après validation des livraisons par l'administrateur.
                </p>
            </div>
        </div>
    </div>
@endsection
