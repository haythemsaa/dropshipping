@extends('layouts.dashboard')

@section('title', 'Modération des produits - Admin')

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
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modération des produits</h1>
        <p class="text-sm text-gray-600 mt-1">Approuvez et gérez les produits du catalogue</p>
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
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total produits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statusCounts['all'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statusCounts['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Approuvés</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statusCounts['approved'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Nom ou SKU..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="approval" class="block text-sm font-medium text-gray-700 mb-1">Approbation</label>
                <select name="approval" id="approval"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ request('approval', 'all') === 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="pending" {{ request('approval') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('approval') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" id="status"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillons</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Products List -->
    @if($products->count() > 0)
        <div class="space-y-4">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Product Image -->
                            @if($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-32 h-32 object-cover rounded-lg">
                            @else
                                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Product Info -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">SKU: {{ $product->sku }} • {{ $product->category->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            Par <a href="{{ route('admin.suppliers.show', $product->supplier) }}" class="text-indigo-600 hover:text-indigo-700">
                                                {{ $product->supplier->business_name ?? $product->supplier->name }}
                                            </a>
                                        </p>
                                    </div>

                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="px-3 py-1 text-sm rounded-full font-medium
                                            @if($product->approved_at) bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            @if($product->approved_at) Approuvé
                                            @else En attente
                                            @endif
                                        </span>

                                        <span class="px-3 py-1 text-sm rounded-full font-medium
                                            @if($product->status === 'active') bg-blue-100 text-blue-800
                                            @elseif($product->status === 'inactive') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-600
                                            @endif">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </div>
                                </div>

                                @if($product->short_description)
                                    <p class="text-sm text-gray-600 mt-3">{{ Str::limit($product->short_description, 200) }}</p>
                                @endif

                                <div class="mt-4 grid grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Prix</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ number_format($product->price, 2) }} DT</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Stock</p>
                                        <p class="text-sm font-semibold @if($product->stock_quantity <= 10) text-red-600 @else text-gray-900 @endif">
                                            {{ $product->stock_quantity }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Ajouté le</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $product->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Ventes</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $product->orderItems()->count() }}</p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 flex items-center space-x-3">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 transition">
                                        Voir détails
                                    </a>

                                    @if(!$product->approved_at)
                                        <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition">
                                                Approuver
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.products.reject', $product) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Rejeter ce produit ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-4 py-2 border border-red-300 text-red-700 rounded-md text-sm hover:bg-red-50 transition">
                                                Rejeter
                                            </button>
                                        </form>
                                    @else
                                        @if($product->orderItems()->count() == 0)
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-4 py-2 border border-red-300 text-red-700 rounded-md text-sm hover:bg-red-50 transition">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun produit trouvé</h3>
            <p class="mt-2 text-sm text-gray-500">Aucun produit ne correspond à vos critères de recherche.</p>
        </div>
    @endif
</div>
@endsection
