@extends('layouts.dashboard')

@section('title', 'Statistiques - Admin')

@section('header', 'Statistiques globales')

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
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commissions
    </a>
    <a href="{{ route('admin.statistics') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
        Statistiques
    </a>
@endsection

@section('content')
    <!-- Period Selector -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('admin.statistics') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                <select name="period" id="period" onchange="this.form.submit()"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="7" {{ request('period', '30') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                    <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="90" {{ request('period', '30') == '90' ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="365" {{ request('period', '30') == '365' ? 'selected' : '' }}>12 derniers mois</option>
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
                        Appliquer
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">GMV Total</h3>
                <div class="bg-green-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($kpis['gmv'], 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">DT</p>
            @if(isset($kpis['gmv_growth']))
                <p class="text-sm mt-2 {{ $kpis['gmv_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $kpis['gmv_growth'] >= 0 ? '+' : '' }}{{ number_format($kpis['gmv_growth'], 1) }}%
                    vs période précédente
                </p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Commissions</h3>
                <div class="bg-indigo-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($kpis['total_commissions'], 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">DT</p>
            <p class="text-sm text-gray-600 mt-2">
                Taux moyen: {{ number_format($kpis['avg_commission_rate'], 1) }}%
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Commandes</h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $kpis['total_orders'] }}</p>
            <p class="text-sm text-gray-500 mt-1">commandes</p>
            @if(isset($kpis['orders_growth']))
                <p class="text-sm mt-2 {{ $kpis['orders_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $kpis['orders_growth'] >= 0 ? '+' : '' }}{{ number_format($kpis['orders_growth'], 1) }}%
                    vs période précédente
                </p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Panier moyen</h3>
                <div class="bg-purple-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($kpis['average_order_value'], 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">DT</p>
        </div>
    </div>

    <!-- Platform Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fournisseurs</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $platformStats['suppliers']['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Actifs</span>
                    <span class="text-sm font-semibold text-green-600">{{ $platformStats['suppliers']['active'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">En attente</span>
                    <span class="text-sm font-semibold text-yellow-600">{{ $platformStats['suppliers']['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Suspendus</span>
                    <span class="text-sm font-semibold text-red-600">{{ $platformStats['suppliers']['suspended'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produits</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $platformStats['products']['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Approuvés</span>
                    <span class="text-sm font-semibold text-green-600">{{ $platformStats['products']['approved'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">En attente</span>
                    <span class="text-sm font-semibold text-yellow-600">{{ $platformStats['products']['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Rupture stock</span>
                    <span class="text-sm font-semibold text-red-600">{{ $platformStats['products']['out_of_stock'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clients</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $platformStats['customers']['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Vérifiés</span>
                    <span class="text-sm font-semibold text-green-600">{{ $platformStats['customers']['verified'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Avec commandes</span>
                    <span class="text-sm font-semibold text-blue-600">{{ $platformStats['customers']['with_orders'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Nouveaux (30j)</span>
                    <span class="text-sm font-semibold text-indigo-600">{{ $platformStats['customers']['new_30d'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Suppliers -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top 10 Fournisseurs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commandes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CA Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topSuppliers as $index => $supplier)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ $supplier->business_name ?? $supplier->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $supplier->products_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $supplier->orders_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($supplier->total_sales, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ number_format($supplier->total_commissions, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($supplier->commission_rate, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucune donnée disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products & Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top 10 Produits</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($topProducts as $index => $product)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 flex-1">
                                <span class="text-sm font-bold text-gray-400">{{ $index + 1 }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->supplier->business_name ?? $product->supplier->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ $product->sales_count }} ventes</p>
                                <p class="text-xs text-gray-500">{{ number_format($product->total_revenue, 0) }} DT</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">Aucune donnée</div>
                @endforelse
            </div>
        </div>

        <!-- Top Categories -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Catégories</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($topCategories as $index => $category)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3 flex-1">
                                <span class="text-sm font-bold text-gray-400">{{ $index + 1 }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $category->products_count }} produits</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ $category->sales_count }} ventes</p>
                                <p class="text-xs text-gray-500">{{ number_format($category->total_revenue, 0) }} DT</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $category->revenue_percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">Aucune donnée</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Répartition des commandes</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                @foreach($ordersByStatus as $status => $count)
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-2
                            @if($status === 'pending') bg-yellow-100
                            @elseif($status === 'confirmed') bg-blue-100
                            @elseif($status === 'processing') bg-indigo-100
                            @elseif($status === 'shipped') bg-purple-100
                            @elseif($status === 'delivered') bg-green-100
                            @elseif($status === 'cancelled') bg-red-100
                            @else bg-gray-100
                            @endif">
                            <span class="text-2xl font-bold
                                @if($status === 'pending') text-yellow-600
                                @elseif($status === 'confirmed') text-blue-600
                                @elseif($status === 'processing') text-indigo-600
                                @elseif($status === 'shipped') text-purple-600
                                @elseif($status === 'delivered') text-green-600
                                @elseif($status === 'cancelled') text-red-600
                                @else text-gray-600
                                @endif">
                                {{ $count }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 capitalize">{{ ucfirst($status) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
