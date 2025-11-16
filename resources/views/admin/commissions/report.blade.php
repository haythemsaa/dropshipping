@extends('layouts.dashboard')

@section('title', 'Rapport de commissions - Admin')

@section('header', 'Rapport de commissions')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Tableau de bord
    </a>
    <a href="{{ route('admin.suppliers.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Fournisseurs
    </a>
    <a href="{{ route('admin.products.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Produits
    </a>
    <a href="{{ route('admin.categories.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Catégories
    </a>
    <a href="{{ route('admin.orders.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commandes
    </a>
    <a href="{{ route('admin.commissions.index') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
        Commissions
    </a>
    <a href="{{ route('admin.statistics') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Statistiques
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mt-1">Analyse détaillée des commissions de la plateforme</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.commissions.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('admin.commissions.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                <select name="period" id="period" onchange="this.form.submit()"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="current_month" {{ request('period', 'current_month') == 'current_month' ? 'selected' : '' }}>Mois en cours</option>
                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                    <option value="current_quarter" {{ request('period') == 'current_quarter' ? 'selected' : '' }}>Trimestre en cours</option>
                    <option value="last_quarter" {{ request('period') == 'last_quarter' ? 'selected' : '' }}>Trimestre dernier</option>
                    <option value="current_year" {{ request('period') == 'current_year' ? 'selected' : '' }}>Année en cours</option>
                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                </select>
            </div>

            @if(request('period') == 'custom')
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
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Générer
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Report Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Période du rapport</h2>
        <p class="text-gray-600">{{ $reportPeriod['start'] }} au {{ $reportPeriod['end'] }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-xs text-gray-600 mb-1">CA Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_sales'], 2) }}</p>
            <p class="text-xs text-gray-500">DT</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-xs text-gray-600 mb-1">Commissions perçues</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($summary['total_commissions'], 2) }}</p>
            <p class="text-xs text-gray-500">DT ({{ number_format($summary['commission_percentage'], 1) }}%)</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-xs text-gray-600 mb-1">Part fournisseurs</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['supplier_amount'], 2) }}</p>
            <p class="text-xs text-gray-500">DT</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-xs text-gray-600 mb-1">Nombre commandes</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total_orders'] }}</p>
            <p class="text-xs text-gray-500">transactions</p>
        </div>
    </div>

    <!-- Commission Status Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-700">En attente</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                    {{ $statusBreakdown['pending']['count'] }}
                </span>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($statusBreakdown['pending']['amount'], 2) }} DT</p>
            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $statusBreakdown['pending']['percentage'] }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-700">Approuvées</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ $statusBreakdown['approved']['count'] }}
                </span>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($statusBreakdown['approved']['amount'], 2) }} DT</p>
            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $statusBreakdown['approved']['percentage'] }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-gray-700">Payées</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ $statusBreakdown['paid']['count'] }}
                </span>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($statusBreakdown['paid']['amount'], 2) }} DT</p>
            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $statusBreakdown['paid']['percentage'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Commissions by Supplier -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Commissions par fournisseur</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commandes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Part fournisseur</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplierBreakdown as $index => $supplier)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $supplier->business_name ?? $supplier->name }}</div>
                                <div class="text-sm text-gray-500">{{ $supplier->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($supplier->commission_rate, 1) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $supplier->order_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($supplier->total_sales, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ number_format($supplier->total_commission, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                {{ number_format($supplier->supplier_amount, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.commissions.supplier', $supplier) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900">Total :</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ number_format($supplierBreakdown->sum('total_sales'), 2) }} DT
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">
                            {{ number_format($supplierBreakdown->sum('total_commission'), 2) }} DT
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">
                            {{ number_format($supplierBreakdown->sum('supplier_amount'), 2) }} DT
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Commission Evolution Chart Placeholder -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des commissions</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Graphique disponible prochainement</p>
            </div>
        </div>
    </div>

    <!-- Top Products by Commission -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top 10 produits par commission</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ventes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission totale</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topProducts as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ number_format($product->price, 2) }} DT</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $product->supplier->business_name ?? $product->supplier->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->sales_count }} unités
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($product->total_revenue, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ number_format($product->total_commission, 2) }} DT
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background: white;
    }

    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
}
</style>
