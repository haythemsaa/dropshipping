@extends('layouts.dashboard')

@section('title', 'Détails produit - Admin')

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
        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
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
        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600">
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
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-600 mt-1">Détails du produit</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($product->approved_at)
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Approuvé
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        En attente
                    </span>
                @endif
                <a href="{{ route('admin.products.index') }}"
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Images -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Images du produit</h3>

                @if($product->images->count() > 0)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image->image_path) }}"
                                     alt="Image produit"
                                     class="w-full h-32 object-cover rounded-lg @if($image->is_primary) ring-2 ring-indigo-500 @endif">
                                @if($image->is_primary)
                                    <span class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-2 py-1 rounded">
                                        Principale
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucune image</p>
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du produit</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Nom</p>
                        <p class="text-base font-medium text-gray-900">{{ $product->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">SKU</p>
                        <p class="text-base font-medium text-gray-900">{{ $product->sku }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Catégorie</p>
                        <p class="text-base font-medium text-gray-900">{{ $product->category->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Statut</p>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($product->status === 'active') bg-green-100 text-green-800
                            @elseif($product->status === 'inactive') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Prix de vente</p>
                        <p class="text-base font-medium text-gray-900">{{ number_format($product->price, 2) }} DT</p>
                    </div>

                    @if($product->cost_price)
                        <div>
                            <p class="text-sm text-gray-600">Prix de revient</p>
                            <p class="text-base font-medium text-gray-900">{{ number_format($product->cost_price, 2) }} DT</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Stock</p>
                        <p class="text-base font-medium @if($product->stock_quantity <= 10) text-red-600 @else text-gray-900 @endif">
                            {{ $product->stock_quantity }} unités
                        </p>
                    </div>

                    @if($product->weight)
                        <div>
                            <p class="text-sm text-gray-600">Poids</p>
                            <p class="text-base font-medium text-gray-900">{{ $product->weight }} kg</p>
                        </div>
                    @endif

                    @if($product->dimensions)
                        <div>
                            <p class="text-sm text-gray-600">Dimensions</p>
                            <p class="text-base font-medium text-gray-900">{{ $product->dimensions }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Produit vedette</p>
                        <p class="text-base font-medium text-gray-900">{{ $product->is_featured ? 'Oui' : 'Non' }}</p>
                    </div>
                </div>

                @if($product->short_description)
                    <div class="mt-6">
                        <p class="text-sm text-gray-600 mb-2">Description courte</p>
                        <p class="text-base text-gray-900">{{ $product->short_description }}</p>
                    </div>
                @endif

                @if($product->description)
                    <div class="mt-6">
                        <p class="text-sm text-gray-600 mb-2">Description détaillée</p>
                        <p class="text-base text-gray-900 whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Sales History -->
            @if($product->orderItems->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Historique des ventes ({{ $product->orderItems->count() }})
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Commande</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Client</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qté</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Prix</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($product->orderItems->take(10) as $item)
                                    <tr class="text-sm">
                                        <td class="px-4 py-2 text-gray-900">{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('admin.orders.show', $item->order) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                #{{ $item->order->order_number }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-gray-900">{{ $item->order->user->name }}</td>
                                        <td class="px-4 py-2 text-right text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 text-right text-gray-900">{{ number_format($item->unit_price, 2) }} DT</td>
                                        <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ number_format($item->subtotal, 2) }} DT</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($item->status === 'delivered') bg-green-100 text-green-800
                                                @elseif($item->status === 'shipped') bg-purple-100 text-purple-800
                                                @elseif($item->status === 'processing') bg-indigo-100 text-indigo-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="5" class="px-4 py-2 text-right text-sm font-medium text-gray-900">
                                        Total des ventes:
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">
                                        {{ number_format($product->orderItems->sum('subtotal'), 2) }} DT
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Supplier Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fournisseur</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nom commercial</p>
                        <a href="{{ route('admin.suppliers.show', $product->supplier) }}"
                           class="text-base font-medium text-indigo-600 hover:text-indigo-700">
                            {{ $product->supplier->business_name ?? $product->supplier->name }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Contact</p>
                        <p class="text-base font-medium text-gray-900">{{ $product->supplier->email }}</p>
                    </div>

                    @if($product->supplier->phone)
                        <div>
                            <p class="text-sm text-gray-600">Téléphone</p>
                            <p class="text-base font-medium text-gray-900">{{ $product->supplier->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ventes totales</span>
                        <span class="text-sm font-bold text-gray-900">{{ $product->orderItems->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Unités vendues</span>
                        <span class="text-sm font-bold text-gray-900">{{ $product->orderItems->sum('quantity') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Revenu total</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($product->orderItems->sum('subtotal'), 2) }} DT</span>
                    </div>
                </div>
            </div>

            <!-- Product Dates -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates importantes</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Créé le</p>
                        <p class="font-medium text-gray-900">{{ $product->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600">Mis à jour le</p>
                        <p class="font-medium text-gray-900">{{ $product->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($product->approved_at)
                        <div>
                            <p class="text-gray-600">Approuvé le</p>
                            <p class="font-medium text-gray-900">{{ $product->approved_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if(!$product->approved_at)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>

                    <div class="space-y-3">
                        <form action="{{ route('admin.products.approve', $product) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Approuver le produit
                            </button>
                        </form>

                        <form action="{{ route('admin.products.reject', $product) }}" method="POST"
                              onsubmit="return confirm('Rejeter ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition">
                                Rejeter le produit
                            </button>
                        </form>
                    </div>
                </div>
            @else
                @if($product->orderItems->count() == 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Danger</h3>

                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Supprimer définitivement ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                Supprimer le produit
                            </button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
