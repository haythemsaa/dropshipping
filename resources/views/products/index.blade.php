@extends('layouts.frontend')

@section('title', 'Catalogue de produits - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="font-semibold text-lg mb-4">Filtres</h3>

                <!-- Categories -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Catégories</h4>
                    <div class="space-y-2">
                        @foreach($categories as $category)
                            <a href="{{ route('products.category', $category->slug) }}"
                               class="block text-gray-600 hover:text-indigo-600 transition">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Prix (TND)</h4>
                    <form action="{{ route('products.index') }}" method="GET" class="space-y-3">
                        <input type="number" name="prix_min" placeholder="Min"
                               value="{{ request('prix_min') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="number" name="prix_max" placeholder="Max"
                               value="{{ request('prix_max') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Filtrer
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Tous les produits
                    <span class="text-gray-500 text-lg font-normal">({{ $products->total() }} produits)</span>
                </h1>

                <!-- Sort -->
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center space-x-2">
                    <label for="tri" class="text-sm text-gray-600">Trier par:</label>
                    <select name="tri" id="tri" onchange="this.form.submit()"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="recent" {{ request('tri') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                        <option value="prix_asc" {{ request('tri') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="prix_desc" {{ request('tri') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="populaire" {{ request('tri') == 'populaire' ? 'selected' : '' }}>Populaires</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <div class="relative h-56 bg-gray-200">
                                    @if($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full text-gray-400">
                                            <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">
                                            Vedette
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="font-semibold text-gray-900 mb-2 hover:text-indigo-600 line-clamp-2">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                <p class="text-sm text-gray-500 mb-3">{{ $product->supplier->business_name }}</p>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} TND</span>
                                        @if($product->stock_quantity > 0)
                                            <p class="text-xs text-green-600 mt-1">En stock</p>
                                        @else
                                            <p class="text-xs text-red-600 mt-1">Rupture de stock</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun produit trouvé</h3>
                    <p class="mt-1 text-gray-500">Essayez d'ajuster vos filtres</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
