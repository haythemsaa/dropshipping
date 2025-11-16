@extends('layouts.dashboard')

@section('title', 'Tableau de bord fournisseur - Dropshipping TN')

@section('header', 'Tableau de bord fournisseur')

@section('sidebar')
    <a href="{{ route('supplier.dashboard') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
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
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Statistiques
    </a>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total produits</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-green-600">{{ $stats['active_products'] }}</span>
                <span class="text-gray-500">actifs</span>
                @if($stats['pending_products'] > 0)
                    <span class="ml-2 text-yellow-600">{{ $stats['pending_products'] }} en attente</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Commandes totales</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-yellow-600">{{ $stats['pending_orders'] }}</span>
                <span class="text-gray-500">en attente</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ventes totales</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_sales'], 0) }}</p>
                    <p class="text-sm text-gray-500">TND</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Commissions</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_commissions'], 0) }}</p>
                    <p class="text-sm text-gray-500">TND prélevées</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Commandes récentes</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentOrders as $orderItem)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">#{{ $orderItem->order->order_number }}</p>
                                <p class="text-sm text-gray-600">{{ $orderItem->product->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($orderItem->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($orderItem->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($orderItem->status === 'shipped') bg-indigo-100 text-indigo-800
                                @elseif($orderItem->status === 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($orderItem->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">{{ $orderItem->created_at->diffForHumans() }}</span>
                            <span class="font-semibold text-indigo-600">{{ number_format($orderItem->subtotal, 2) }} TND</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Aucune commande récente
                    </div>
                @endforelse
            </div>
            @if($recentOrders->count() > 0)
                <div class="p-4 border-t border-gray-200">
                    <a href="{{ route('supplier.orders.index') }}"
                       class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        Voir toutes les commandes →
                    </a>
                </div>
            @endif
        </div>

        <!-- Popular Products & Out of Stock -->
        <div class="space-y-6">
            <!-- Popular Products -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Produits populaires</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($popularProducts as $product)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $product->sales_count }} ventes</p>
                                </div>
                                <span class="text-indigo-600 font-semibold">{{ number_format($product->price, 2) }} TND</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            Aucun produit populaire
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Out of Stock -->
            @if($outOfStockProducts->count() > 0)
                <div class="bg-white rounded-lg shadow-md border-l-4 border-red-500">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-red-900">
                            Produits en rupture de stock
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($outOfStockProducts as $product)
                            <div class="p-6">
                                <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-red-600">Stock: {{ $product->stock_quantity }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <a href="{{ route('supplier.products.index') }}"
                           class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Gérer le stock →
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
