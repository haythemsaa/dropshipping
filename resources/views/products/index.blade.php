@extends('layouts.frontend')

@section('title', 'Catalogue de produits - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête avec recherche -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Catalogue de produits</h1>

        <!-- Barre de recherche -->
        <div class="relative max-w-2xl" x-data="{ open: false, results: [] }">
            <form action="{{ route('products.search') }}" method="GET">
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Rechercher un produit..."
                           @input.debounce.300ms="
                               if ($el.value.length >= 2) {
                                   fetch('{{ route('products.autocomplete') }}?q=' + encodeURIComponent($el.value))
                                       .then(r => r.json())
                                       .then(data => { results = data; open = data.length > 0; });
                               } else {
                                   open = false;
                               }
                           "
                           @click.away="open = false"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>

            <!-- Autocomplete Results -->
            <div x-show="open"
                 x-transition
                 class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto">
                <template x-for="result in results" :key="result.id">
                    <a :href="result.url"
                       class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-center">
                            <span x-text="result.name" class="text-gray-900 font-medium"></span>
                            <span x-text="result.price + ' TND'" class="text-indigo-600 font-semibold"></span>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-72 flex-shrink-0">
            <!-- Bouton mobile -->
            <div class="lg:hidden mb-4">
                <button @click="$refs.mobileFilters.classList.toggle('hidden')"
                        class="w-full flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres
                </button>
            </div>

            <div ref="mobileFilters" class="hidden lg:block bg-white rounded-lg shadow-md p-6 sticky top-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Filtres</h3>
                    @if(request()->hasAny(['categories', 'prix_min', 'prix_max', 'note_min', 'en_stock']))
                        <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Réinitialiser
                        </a>
                    @endif
                </div>

                <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                    <!-- Recherche (hidden) -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <!-- Catégories -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-3">Catégories</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($categories as $category)
                                <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox"
                                           name="categories[]"
                                           value="{{ $category->id }}"
                                           {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                </label>
                                @if($category->children && $category->children->count() > 0)
                                    <div class="ml-6 space-y-1 mt-1">
                                        @foreach($category->children as $child)
                                            <label class="flex items-start cursor-pointer hover:bg-gray-50 p-1 rounded">
                                                <input type="checkbox"
                                                       name="categories[]"
                                                       value="{{ $child->id }}"
                                                       {{ in_array($child->id, request('categories', [])) ? 'checked' : '' }}
                                                       onchange="document.getElementById('filterForm').submit()"
                                                       class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-xs text-gray-600">{{ $child->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-3">Prix (TND)</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <input type="number"
                                       name="prix_min"
                                       placeholder="Min"
                                       value="{{ request('prix_min') }}"
                                       min="0"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <span class="text-gray-500">-</span>
                                <input type="number"
                                       name="prix_max"
                                       placeholder="Max"
                                       value="{{ request('prix_max') }}"
                                       min="0"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <button type="submit"
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium transition">
                                Appliquer
                            </button>
                            <p class="text-xs text-gray-500 text-center">
                                {{ number_format($stats['prix_min'], 0) }} - {{ number_format($stats['prix_max'], 0) }} TND
                            </p>
                        </div>
                    </div>

                    <!-- Note minimum -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-3">Note minimum</h4>
                        <div class="space-y-2">
                            @for($i = 5; $i >= 1; $i--)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="radio"
                                           name="note_min"
                                           value="{{ $i }}"
                                           {{ request('note_min') == $i ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 flex items-center text-sm">
                                        @for($j = 1; $j <= $i; $j++)
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-gray-600">& plus</span>
                                    </span>
                                </label>
                            @endfor
                            @if(request('note_min'))
                                <button type="button"
                                        onclick="document.getElementsByName('note_min').forEach(r => r.checked = false); document.getElementById('filterForm').submit();"
                                        class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    Effacer
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="mb-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox"
                                   name="en_stock"
                                   value="1"
                                   {{ request('en_stock') == '1' ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Uniquement en stock</span>
                        </label>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        @if(request('q'))
                            Résultats pour "{{ request('q') }}"
                        @elseif(request('categories'))
                            Produits filtrés
                        @else
                            Tous les produits
                        @endif
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['total'] }} {{ Str::plural('produit', $stats['total']) }} trouvé{{ $stats['total'] > 1 ? 's' : '' }}</p>
                </div>

                <!-- Sort -->
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center space-x-2">
                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if(request('categories'))
                        @foreach(request('categories') as $cat)
                            <input type="hidden" name="categories[]" value="{{ $cat }}">
                        @endforeach
                    @endif
                    @if(request('prix_min'))<input type="hidden" name="prix_min" value="{{ request('prix_min') }}">@endif
                    @if(request('prix_max'))<input type="hidden" name="prix_max" value="{{ request('prix_max') }}">@endif
                    @if(request('note_min'))<input type="hidden" name="note_min" value="{{ request('note_min') }}">@endif
                    @if(request('en_stock'))<input type="hidden" name="en_stock" value="{{ request('en_stock') }}">@endif

                    <label for="tri" class="text-sm text-gray-600 whitespace-nowrap">Trier par:</label>
                    <select name="tri" id="tri" onchange="this.form.submit()"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="recent" {{ request('tri') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                        <option value="populaire" {{ request('tri') == 'populaire' ? 'selected' : '' }}>Populaires</option>
                        <option value="meilleures_notes" {{ request('tri') == 'meilleures_notes' ? 'selected' : '' }}>Mieux notés</option>
                        <option value="prix_asc" {{ request('tri') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="prix_desc" {{ request('tri') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="nom_asc" {{ request('tri') == 'nom_asc' ? 'selected' : '' }}>Nom A-Z</option>
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

                                    <!-- Wishlist Button -->
                                    @auth
                                        <div x-data="{
                                            inWishlist: {{ auth()->user()->hasInWishlist($product->id) ? 'true' : 'false' }},
                                            toggleWishlist() {
                                                fetch('{{ route('wishlist.toggle', $product) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json'
                                                    }
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    this.inWishlist = data.action === 'added';
                                                    // Update header counter
                                                    if (document.getElementById('wishlistCount')) {
                                                        document.getElementById('wishlistCount').textContent = data.wishlistCount;
                                                    }
                                                });
                                            }
                                        }" class="absolute top-2 left-2">
                                            <button @click="toggleWishlist()"
                                                    type="button"
                                                    class="bg-white rounded-full p-2 shadow-md hover:bg-gray-50 transition">
                                                <svg class="h-5 w-5 transition"
                                                     :class="inWishlist ? 'text-red-500 fill-current' : 'text-gray-400'"
                                                     viewBox="0 0 24 24">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endauth

                                    @if($product->is_featured)
                                        <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">
                                            Vedette
                                        </span>
                                    @endif
                                    @if($product->stock_quantity == 0)
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <span class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">
                                                Rupture de stock
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="font-semibold text-gray-900 mb-2 hover:text-indigo-600 line-clamp-2">
                                        {{ $product->name }}
                                    </h3>
                                </a>

                                <!-- Rating -->
                                @if($product->reviewsCount > 0)
                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= round($product->averageRating) ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                                     viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-1 text-xs text-gray-600">({{ $product->reviewsCount }})</span>
                                    </div>
                                @endif

                                <p class="text-sm text-gray-500 mb-3">{{ $product->supplier->business_name }}</p>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} TND</span>
                                        @if($product->stock_quantity > 0)
                                            <p class="text-xs text-green-600 mt-1">En stock</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit"
                                                @if($product->stock_quantity == 0) disabled @endif
                                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun produit trouvé</h3>
                    <p class="mt-2 text-gray-600">Essayez de modifier vos filtres ou votre recherche.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                            Voir tous les produits
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
