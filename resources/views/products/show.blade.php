@extends('layouts.frontend')

@section('title', $product->name . ' - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Accueil</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-600">Produits</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-indigo-600">
            {{ $product->category->name }}
        </a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div>
            <div class="bg-gray-100 rounded-lg overflow-hidden mb-4">
                @if($product->images->count() > 0)
                    <img src="{{ Storage::url($product->images->where('is_primary', true)->first()->image_path ?? $product->images->first()->image_path) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-contain"
                         id="main-image">
                @else
                    <div class="flex items-center justify-center h-96 text-gray-400">
                        <svg class="h-32 w-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                        <button onclick="changeImage('{{ Storage::url($image->image_path) }}')"
                                class="bg-gray-100 rounded-lg overflow-hidden hover:ring-2 hover:ring-indigo-600 transition">
                            <img src="{{ Storage::url($image->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-20 object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center mb-6">
                <span class="text-4xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} TND</span>
            </div>

            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Fournisseur:</span>
                    <span class="font-semibold">{{ $product->supplier->business_name }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Catégorie:</span>
                    <a href="{{ route('products.category', $product->category->slug) }}"
                       class="text-indigo-600 hover:text-indigo-700">
                        {{ $product->category->name }}
                    </a>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">SKU:</span>
                    <span class="font-mono text-sm">{{ $product->sku }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Disponibilité:</span>
                    @if($product->isInStock())
                        <span class="text-green-600 font-semibold">En stock ({{ $product->stock_quantity }} unités)</span>
                    @else
                        <span class="text-red-600 font-semibold">Rupture de stock</span>
                    @endif
                </div>
            </div>

            <!-- Add to Cart Form -->
            @if($product->isInStock())
                <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="flex items-center space-x-4 mb-4">
                        <label for="quantity" class="text-gray-700 font-medium">Quantité:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                               class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @auth
                        @if(auth()->user()->isClient())
                            <button type="submit"
                                    class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Ajouter au panier
                            </button>
                        @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <p class="text-yellow-700">Seuls les clients peuvent ajouter des produits au panier.</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-gray-600 mb-3">Connectez-vous pour commander ce produit</p>
                            <a href="{{ route('login') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                Se connecter
                            </a>
                        </div>
                    @endauth
                </form>
            @else
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <p class="text-red-700">Ce produit est actuellement en rupture de stock</p>
                </div>
            @endif

            <!-- Product Details -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                @if($product->short_description)
                    <p class="text-gray-700 mb-4">{{ $product->short_description }}</p>
                @endif
                @if($product->description)
                    <div class="text-gray-600 prose max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif

                @if($product->weight || $product->dimensions)
                    <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Spécifications</h3>
                    <ul class="space-y-2">
                        @if($product->weight)
                            <li class="flex items-center text-gray-600">
                                <span class="font-medium mr-2">Poids:</span>
                                {{ $product->weight }} kg
                            </li>
                        @endif
                        @if($product->dimensions)
                            <li class="flex items-center text-gray-600">
                                <span class="font-medium mr-2">Dimensions:</span>
                                {{ $product->dimensions }}
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produits similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}">
                            <div class="h-48 bg-gray-200">
                                @if($relatedProduct->images->first())
                                    <img src="{{ Storage::url($relatedProduct->images->first()->image_path) }}"
                                         alt="{{ $relatedProduct->name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}">
                                <h3 class="font-semibold text-gray-900 hover:text-indigo-600 line-clamp-2">
                                    {{ $relatedProduct->name }}
                                </h3>
                            </a>
                            <p class="text-xl font-bold text-indigo-600 mt-2">
                                {{ number_format($relatedProduct->price, 2) }} TND
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
function changeImage(url) {
    document.getElementById('main-image').src = url;
}
</script>
@endsection
