@extends('layouts.dashboard')

@section('title', 'Détails fournisseur - Admin')

@section('sidebar')
<aside class="w-64 bg-white shadow-md">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900">Administration</h2>
        <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
    </div>
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Fournisseurs
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Catégories
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Produits
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Commandes
        </a>
        <a href="{{ route('admin.commissions.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Commissions
        </a>
    </nav>
</aside>
@endsection

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->business_name ?? $user->name }}</h1>
                <p class="text-sm text-gray-600 mt-1">Détails du fournisseur</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($user->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($user->status === 'active') bg-green-100 text-green-800
                    @elseif($user->status === 'suspended') bg-red-100 text-red-800
                    @elseif($user->status === 'rejected') bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($user->status) }}
                </span>
                <a href="{{ route('admin.suppliers.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Produits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_products'] }} actifs</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ventes totales</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sales'], 2) }} DT</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_orders'] }} commandes</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Commandes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['pending_orders'] }} en attente</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Supplier Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du fournisseur</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Nom complet</p>
                        <p class="text-base font-medium text-gray-900">{{ $user->name }}</p>
                    </div>

                    @if($user->business_name)
                        <div>
                            <p class="text-sm text-gray-600">Nom commercial</p>
                            <p class="text-base font-medium text-gray-900">{{ $user->business_name }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-base font-medium text-gray-900">{{ $user->email }}</p>
                    </div>

                    @if($user->phone)
                        <div>
                            <p class="text-sm text-gray-600">Téléphone</p>
                            <p class="text-base font-medium text-gray-900">{{ $user->phone }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Taux de commission</p>
                        <p class="text-base font-medium text-gray-900">{{ number_format($user->commission_rate ?? 10, 2) }}%</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Inscrit le</p>
                        <p class="text-base font-medium text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($user->approved_at)
                        <div>
                            <p class="text-sm text-gray-600">Approuvé le</p>
                            <p class="text-base font-medium text-gray-900">{{ $user->approved_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Produits ({{ $user->products->count() }})</h3>
                    <a href="{{ route('admin.products.index', ['supplier' => $user->id]) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tous →
                    </a>
                </div>

                @if($user->products->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->products->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($product->price, 2) }} DT • Stock: {{ $product->stock_quantity }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full
                                        @if($product->status === 'active') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $product->status === 'active' ? 'Actif' : 'Inactif' }}
                                    </span>
                                    @if($product->approved_at)
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Aucun produit</p>
                @endif
            </div>

            <!-- Recent Commissions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Commissions récentes</h3>
                    <a href="{{ route('admin.commissions.supplier', $user) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir toutes →
                    </a>
                </div>

                @if($commissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Commande</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Montant fournisseur</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commission</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($commissions as $commission)
                                    <tr class="text-sm">
                                        <td class="px-4 py-2 text-gray-900">{{ $commission->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-gray-900">#{{ $commission->orderItem->order->order_number }}</td>
                                        <td class="px-4 py-2 text-right text-gray-900">{{ number_format($commission->supplier_amount, 2) }} DT</td>
                                        <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ number_format($commission->commission_amount, 2) }} DT</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($commission->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($commission->status === 'paid') bg-green-100 text-green-800
                                                @endif">
                                                {{ $commission->status === 'pending' ? 'En attente' : 'Payée' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Aucune commission</p>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>

                <div class="space-y-3">
                    @if($user->status === 'pending')
                        <form action="{{ route('admin.suppliers.approve', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Approuver le fournisseur
                            </button>
                        </form>

                        <form action="{{ route('admin.suppliers.reject', $user) }}" method="POST"
                              onsubmit="return confirm('Rejeter ce fournisseur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition">
                                Rejeter la demande
                            </button>
                        </form>
                    @endif

                    @if($user->status === 'active')
                        <form action="{{ route('admin.suppliers.suspend', $user) }}" method="POST"
                              onsubmit="return confirm('Suspendre ce fournisseur ? Il ne pourra plus vendre.')">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Suspendre le compte
                            </button>
                        </form>
                    @endif

                    @if($user->status === 'suspended')
                        <form action="{{ route('admin.suppliers.activate', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Réactiver le compte
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de commission</h3>

                <form action="{{ route('admin.suppliers.updateCommission', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Taux de commission (%)
                        </label>
                        <input type="number" name="commission_rate" id="commission_rate"
                               min="0" max="100" step="0.01"
                               value="{{ $user->commission_rate ?? 10 }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Taux par défaut: 10%</p>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Account Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations compte</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Email vérifié</p>
                        <p class="font-medium text-gray-900">
                            @if($user->email_verified_at)
                                <span class="text-green-600">✓ Oui</span>
                                <br>
                                <span class="text-xs text-gray-500">{{ $user->email_verified_at->format('d/m/Y') }}</span>
                            @else
                                <span class="text-red-600">✗ Non</span>
                            @endif
                        </p>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-gray-600">Dernière connexion</p>
                        <p class="font-medium text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
