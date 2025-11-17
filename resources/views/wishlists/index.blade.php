@extends('layouts.frontend')

@section('title', 'Mes favoris - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mes favoris</h1>
        <p class="text-gray-600 mt-2">{{ $wishlists->total() }} {{ Str::plural('produit', $wishlists->total()) }} dans votre liste de souhaits</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
                @if($wishlist->product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <a href="{{ route('products.show', $wishlist->product->slug) }}">
                            <div class="relative h-56 bg-gray-200">
                                @if($wishlist->product->images->first())
                                    <img src="{{ Storage::url($wishlist->product->images->first()->image_path) }}"
                                         alt="{{ $wishlist->product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400">
                                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Remove button -->
                                <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition"
                                            onclick="return confirm('Retirer ce produit de vos favoris ?')">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </button>
                                </form>

                                @if($wishlist->product->is_featured)
                                    <span class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">
                                        Vedette
                                    </span>
                                @endif

                                @if($wishlist->product->stock_quantity == 0)
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                        <span class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">
                                            Rupture de stock
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('products.show', $wishlist->product->slug) }}">
                                <h3 class="font-semibold text-gray-900 mb-2 hover:text-indigo-600 line-clamp-2">
                                    {{ $wishlist->product->name }}
                                </h3>
                            </a>

                            <!-- Rating -->
                            @if($wishlist->product->reviewsCount > 0)
                                <div class="flex items-center mb-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($wishlist->product->averageRating) ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                                 viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-1 text-xs text-gray-600">({{ $wishlist->product->reviewsCount }})</span>
                                </div>
                            @endif

                            <p class="text-sm text-gray-500 mb-3">{{ $wishlist->product->supplier->business_name }}</p>

                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($wishlist->product->price, 2) }} TND</span>
                                    @if($wishlist->product->stock_quantity > 0)
                                        <p class="text-xs text-green-600 mt-1">En stock</p>
                                    @endif
                                </div>

                                @if($wishlist->product->stock_quantity > 0)
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $wishlist->product->id }}">
                                        <button type="submit"
                                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm font-medium">
                                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 mt-3">Ajouté le {{ $wishlist->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        @if($wishlists->hasPages())
            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16 bg-white rounded-lg shadow-md">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Votre liste de favoris est vide</h3>
            <p class="mt-2 text-gray-600">Parcourez notre catalogue et ajoutez vos produits préférés !</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 transition">
                    Découvrir les produits
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
