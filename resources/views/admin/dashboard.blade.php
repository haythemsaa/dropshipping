@extends('layouts.dashboard')

@section('title', 'Administration - Dropshipping TN')

@section('header', 'Tableau de bord administrateur')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
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
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Statistiques
    </a>
@endsection

@section('content')
    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Fournisseurs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_suppliers'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-green-600">{{ $stats['active_suppliers'] }}</span>
                <span class="text-gray-500">actifs</span>
                @if($stats['pending_suppliers'] > 0)
                    <span class="ml-2 text-yellow-600">{{ $stats['pending_suppliers'] }} en attente</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Produits</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-green-600">{{ $stats['active_products'] }}</span>
                <span class="text-gray-500">actifs</span>
                @if($stats['pending_products'] > 0)
                    <span class="ml-2 text-yellow-600">{{ $stats['pending_products'] }} à approuver</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Revenus totaux</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0) }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_commissions'], 0) }}</p>
                    <p class="text-sm text-gray-500">TND</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-sm">
                <span class="text-yellow-600">{{ number_format($stats['pending_commissions'], 0) }}</span>
                <span class="text-gray-500">TND en attente</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Pending Suppliers -->
        @if($pendingSuppliers->count() > 0)
            <div class="bg-white rounded-lg shadow-md border-l-4 border-yellow-500">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Fournisseurs en attente
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                            {{ $pendingSuppliers->count() }}
                        </span>
                    </h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($pendingSuppliers as $supplier)
                        <div class="p-4 hover:bg-gray-50">
                            <p class="font-semibold text-gray-900">{{ $supplier->business_name }}</p>
                            <p class="text-sm text-gray-600">{{ $supplier->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $supplier->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-gray-200">
                    <a href="{{ route('admin.suppliers.index', ['status' => 'pending']) }}"
                       class="text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                        Gérer les demandes →
                    </a>
                </div>
            </div>
        @endif

        <!-- Pending Products -->
        @if($pendingProducts->count() > 0)
            <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-500">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Produits à approuver
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ $pendingProducts->count() }}
                        </span>
                    </h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($pendingProducts as $product)
                        <div class="p-4 hover:bg-gray-50">
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-600">{{ $product->supplier->business_name }}</p>
                            <p class="text-sm text-indigo-600 mt-1">{{ number_format($product->price, 2) }} TND</p>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-gray-200">
                    <a href="{{ route('admin.products.index', ['approval' => 'pending']) }}"
                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Modérer les produits →
                    </a>
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Statistiques rapides</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Commandes totales</span>
                    <span class="font-semibold text-gray-900">{{ $stats['total_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Commandes en attente</span>
                    <span class="font-semibold text-yellow-600">{{ $stats['pending_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total clients</span>
                    <span class="font-semibold text-gray-900">{{ $stats['total_clients'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Commandes récentes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Numéro
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($order->total, 2) }} TND
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Aucune commande récente
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recentOrders->count() > 0)
            <div class="p-4 border-t border-gray-200">
                <a href="{{ route('admin.orders.index') }}"
                   class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                    Voir toutes les commandes →
                </a>
            </div>
        @endif
    </div>
@endsection
