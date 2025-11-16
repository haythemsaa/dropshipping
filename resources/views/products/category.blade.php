@extends('layouts.frontend')

@section('title', $category->name . ' - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-indigo-600">Accueil</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-indigo-600">Produits</a></li>
            @if($category->parent)
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('products.category', $category->parent->slug) }}" class="hover:text-indigo-600">
                    {{ $category->parent->name }}
                </a></li>
            @endif
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-900 font-medium">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-gray-600">{{ $category->description }}</p>
        @endif
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="font-semibold text-lg mb-4">Filtres</h3>

                <!-- Sub-categories -->
                @if($category->children->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Sous-catégories</h4>
                        <div class="space-y-2">
                            <a href="{{ route('products.category', $category->slug) }}"
                               class="block px-3 py-2 rounded {{ !request('subcategory') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                Toutes
                            </a>
                            @foreach($category->children as $child)
                                <a href="{{ route('products.category', ['category' => $category->slug, 'subcategory' => $child->slug]) }}"
                                   class="block px-3 py-2 rounded {{ request('subcategory') == $child->slug ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- All Categories -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Autres catégories</h4>
                    <div class="space-y-2">
                        @foreach($allCategories->where('id', '!=', $category->id) as $cat)
                            <a href="{{ route('products.category', $cat->slug) }}"
                               class="block text-gray-600 hover:text-indigo-600 transition text-sm">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Prix (TND)</h4>
                    <form action="{{ route('products.category', $category->slug) }}" method="GET" class="space-y-3">
                        @if(request('subcategory'))
                            <input type="hidden" name="subcategory" value="{{ request('subcategory') }}">
                        @endif
                        <input type="number" name="prix_min" placeholder="Min"
                               value="{{ request('prix_min') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="number" name="prix_max" placeholder="Max"
                               value="{{ request('prix_max') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                            Filtrer
                        </button>
                    </form>
                </div>

                <!-- Stock Filter -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Disponibilité</h4>
                    <form action="{{ route('products.category', $category->slug) }}" method="GET">
                        @if(request('subcategory'))
                            <input type="hidden" name="subcategory" value="{{ request('subcategory') }}">
                        @endif
                        @if(request('prix_min'))
                            <input type="hidden" name="prix_min" value="{{ request('prix_min') }}">
                        @endif
                        @if(request('prix_max'))
                            <input type="hidden" name="prix_max" value="{{ request('prix_max') }}">
                        @endif
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="en_stock" value="1"
                                   {{ request('en_stock') ? 'checked' : '' }}
                                   onchange="this.form.submit()"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">En stock uniquement</span>
                        </label>
                    </form>
                </div>

                <!-- Reset Filters -->
                @if(request()->hasAny(['prix_min', 'prix_max', 'en_stock', 'subcategory']))
                    <a href="{{ route('products.category', $category->slug) }}"
                       class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $products->total() }} produit{{ $products->total() > 1 ? 's' : '' }} trouvé{{ $products->total() > 1 ? 's' : '' }}
                </h2>

                <!-- Sort -->
                <form action="{{ route('products.category', $category->slug) }}" method="GET" class="flex items-center space-x-2">
                    @if(request('subcategory'))
                        <input type="hidden" name="subcategory" value="{{ request('subcategory') }}">
                    @endif
                    @if(request('prix_min'))
                        <input type="hidden" name="prix_min" value="{{ request('prix_min') }}">
                    @endif
                    @if(request('prix_max'))
                        <input type="hidden" name="prix_max" value="{{ request('prix_max') }}">
                    @endif
                    @if(request('en_stock'))
                        <input type="hidden" name="en_stock" value="1">
                    @endif
                    <label for="tri" class="text-sm text-gray-600">Trier par:</label>
                    <select name="tri" id="tri" onchange="this.form.submit()"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="recent" {{ request('tri') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                        <option value="prix_asc" {{ request('tri') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="prix_desc" {{ request('tri') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="populaire" {{ request('tri') == 'populaire' ? 'selected' : '' }}>Populaires</option>
                        <option value="nom" {{ request('tri') == 'nom' ? 'selected' : '' }}>Nom A-Z</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <div class="relative h-56 bg-gray-200 overflow-hidden">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                                    @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                        <span class="absolute top-2 left-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">
                                            Plus que {{ $product->stock_quantity }} !
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="font-semibold text-gray-900 mb-2 hover:text-indigo-600 line-clamp-2 min-h-[3rem]">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                <p class="text-sm text-gray-500 mb-3">{{ $product->supplier->business_name ?? $product->supplier->name }}</p>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} DT</span>
                                        @if($product->stock_quantity > 0)
                                            <p class="text-xs text-green-600 mt-1">En stock</p>
                                        @else
                                            <p class="text-xs text-red-600 mt-1">Rupture de stock</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        @if($product->stock_quantity > 0)
                                            <button type="submit"
                                                    class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" disabled
                                                    class="bg-gray-300 text-gray-500 p-2 rounded-full cursor-not-allowed">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun produit trouvé</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Aucun produit ne correspond à vos critères de recherche dans cette catégorie.
                    </p>
                    @if(request()->hasAny(['prix_min', 'prix_max', 'en_stock', 'subcategory']))
                        <a href="{{ route('products.category', $category->slug) }}"
                           class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Afficher tous les produits
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
