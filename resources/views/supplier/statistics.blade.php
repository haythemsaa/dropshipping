@extends('layouts.dashboard')

@section('title', 'Statistiques - Fournisseur')

@section('header', 'Statistiques détaillées')

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
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commissions
    </a>
    <a href="{{ route('supplier.statistics') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
        Statistiques
    </a>
@endsection

@section('content')
    <!-- Period Selector -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('supplier.statistics') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Revenus totaux</h3>
                <div class="bg-green-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">DT</p>
            @if(isset($stats['revenue_growth']))
                <p class="text-sm mt-2 {{ $stats['revenue_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['revenue_growth'], 1) }}%
                    vs période précédente
                </p>
            @endif
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
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
            <p class="text-sm text-gray-500 mt-1">commandes</p>
            @if(isset($stats['orders_growth']))
                <p class="text-sm mt-2 {{ $stats['orders_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stats['orders_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['orders_growth'], 1) }}%
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
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['average_order_value'], 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">DT</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Produits vendus</h3>
                <div class="bg-indigo-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['units_sold'] }}</p>
            <p class="text-sm text-gray-500 mt-1">unités</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart Placeholder -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des revenus</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Graphique disponible prochainement</p>
                </div>
            </div>
        </div>

        <!-- Orders Chart Placeholder -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition des commandes</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Graphique disponible prochainement</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Produits les plus vendus</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ventes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unités</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProducts as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="h-10 w-10 rounded object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($product->price, 2) }} DT</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->sales_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->units_sold }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($product->total_revenue, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                -{{ number_format($product->total_commission, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ number_format($product->net_revenue, 2) }} DT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucune vente sur cette période
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance by Category -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performance par catégorie</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ventes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% du total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categoryStats as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $category->products_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $category->sales_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($category->total_revenue, 2) }} DT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-indigo-600 h-2 rounded-full"
                                             style="width: {{ $category->revenue_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ number_format($category->revenue_percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucune donnée disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alert -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Alertes stock faible</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($lowStockProducts as $product)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-red-600 mt-1">Stock: {{ $product->stock_quantity }} unités</p>
                            </div>
                            <a href="{{ route('supplier.products.edit', $product) }}"
                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Réapprovisionner →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm">Tous les stocks sont au bon niveau</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Commandes en attente</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($pendingOrders as $orderItem)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">#{{ $orderItem->order->order_number }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $orderItem->product->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $orderItem->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('supplier.orders.show', $orderItem->order) }}"
                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Traiter →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm">Aucune commande en attente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
