@extends('layouts.frontend')

@section('title', 'Accueil - Dropshipping Marketplace Tunisia')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Bienvenue sur Dropshipping TN
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                La première plateforme de dropshipping en Tunisie
            </p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('products.index') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Découvrir les produits
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-indigo-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-900 transition">
                        Devenir vendeur
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Categories -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Catégories populaires</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('products.category', $category->slug) }}"
               class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
                <div class="text-4xl mb-3">📦</div>
                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $category->products_count ?? 0 }} produits</p>
            </a>
        @endforeach
    </div>
</div>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<div class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Produits vedettes</h2>
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                Voir tout →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="relative h-48 bg-gray-200">
                        @if($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">
                            Vedette
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $product->supplier->business_name }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} TND</span>
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- New Products -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Nouveautés</h2>
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
            Voir tout →
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <div class="relative h-48 bg-gray-200">
                    @if($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image_path) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    @if($product->created_at->diffInDays(now()) < 7)
                        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                            Nouveau
                        </span>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ $product->supplier->business_name }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} TND</span>
                        <a href="{{ route('products.show', $product->slug) }}"
                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                            Voir
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Features Section -->
<div class="bg-indigo-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Pourquoi nous choisir ?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Sans investissement</h3>
                <p class="text-gray-600">Commencez à vendre sans stock initial</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Paiement sécurisé</h3>
                <p class="text-gray-600">Transactions 100% sécurisées</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Livraison rapide</h3>
                <p class="text-gray-600">Expédition dans toute la Tunisie</p>
            </div>
        </div>
    </div>
</div>
@endsection
