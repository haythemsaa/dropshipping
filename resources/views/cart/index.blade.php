@extends('layouts.frontend')

@section('title', 'Mon panier - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Mon panier</h1>

    @if($cart && $items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @foreach($items as $item)
                        <div class="p-6 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @php
                                        $imageUrl = null;
                                        if ($item->variant_id && $item->variant && $item->variant->image_path) {
                                            $imageUrl = Storage::url($item->variant->image_path);
                                        } elseif ($item->product->images->first()) {
                                            $imageUrl = Storage::url($item->product->images->first()->image_path);
                                        }
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}"
                                             alt="{{ $item->getDisplayName() }}"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}"
                                       class="font-semibold text-gray-900 hover:text-indigo-600">
                                        {{ $item->getDisplayName() }}
                                    </a>
                                    @if($item->variant_id && $item->variant)
                                        <p class="text-xs text-gray-500 mt-1">SKU: {{ $item->variant->sku }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1">{{ $item->product->supplier->business_name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Prix unitaire: {{ number_format($item->price, 2) }} TND</p>

                                    @php
                                        $availableStock = $item->variant_id && $item->variant
                                            ? $item->variant->stock_quantity
                                            : $item->product->stock_quantity;
                                    @endphp
                                    @if($availableStock < $item->quantity)
                                        <p class="text-sm text-red-600 mt-1">
                                            Stock insuffisant (disponible: {{ $availableStock }})
                                        </p>
                                    @endif
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        @php
                                            $maxStock = $item->variant_id && $item->variant
                                                ? $item->variant->stock_quantity
                                                : $item->product->stock_quantity;
                                        @endphp
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                               max="{{ $maxStock }}"
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               onchange="this.form.submit()">
                                    </form>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right min-w-[100px]">
                                    <p class="font-semibold text-gray-900">
                                        {{ number_format($item->price * $item->quantity, 2) }} TND
                                    </p>
                                </div>

                                <!-- Remove Button -->
                                <div>
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Clear Cart Button -->
                <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm"
                            onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                        Vider le panier
                    </button>
                </form>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Résumé</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-semibold">{{ number_format($cart->getTotal(), 2) }} TND</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais de livraison</span>
                            <span class="font-semibold">7.00 TND</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-indigo-600">
                                {{ number_format($cart->getTotal() + 7.00, 2) }} TND
                            </span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->isClient())
                            <a href="{{ route('orders.checkout') }}"
                               class="block w-full bg-indigo-600 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                Passer la commande
                            </a>
                        @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <p class="text-sm text-yellow-700">Seuls les clients peuvent passer commande</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-gray-600 text-sm mb-3">Connectez-vous pour finaliser votre commande</p>
                            <a href="{{ route('login') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                Se connecter
                            </a>
                        </div>
                    @endauth

                    <a href="{{ route('products.index') }}"
                       class="block w-full text-center text-indigo-600 mt-4 hover:text-indigo-700">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-2xl font-semibold text-gray-900">Votre panier est vide</h3>
            <p class="mt-2 text-gray-600">Ajoutez des produits à votre panier pour commencer</p>
            <a href="{{ route('products.index') }}"
               class="mt-6 inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Découvrir les produits
            </a>
        </div>
    @endif
</div>
@endsection
