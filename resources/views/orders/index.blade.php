@extends('layouts.frontend')

@section('title', 'Mes commandes - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mes commandes</h1>
        <p class="mt-2 text-sm text-gray-600">Suivez et gérez vos commandes</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('orders.index') }}"
               class="@if(!request('status')) border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Toutes
                <span class="@if(!request('status')) bg-indigo-100 text-indigo-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                    {{ $orders->total() }}
                </span>
            </a>
            <a href="{{ route('orders.index', ['status' => 'pending']) }}"
               class="@if(request('status') === 'pending') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                En attente
            </a>
            <a href="{{ route('orders.index', ['status' => 'processing']) }}"
               class="@if(request('status') === 'processing') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                En préparation
            </a>
            <a href="{{ route('orders.index', ['status' => 'shipped']) }}"
               class="@if(request('status') === 'shipped') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Expédiées
            </a>
            <a href="{{ route('orders.index', ['status' => 'delivered']) }}"
               class="@if(request('status') === 'delivered') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Livrées
            </a>
        </nav>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <!-- Order Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <p class="text-sm text-gray-600">Commande</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</p>
                                </div>
                                <div class="hidden sm:block h-8 w-px bg-gray-300"></div>
                                <div class="hidden sm:block">
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="hidden md:block h-8 w-px bg-gray-300"></div>
                                <div class="hidden md:block">
                                    <p class="text-sm text-gray-600">Total</p>
                                    <p class="text-sm font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} DT</p>
                                </div>
                            </div>
                            <div class="mt-3 sm:mt-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    @if($order->status === 'pending') En attente
                                    @elseif($order->status === 'confirmed') Confirmée
                                    @elseif($order->status === 'processing') En préparation
                                    @elseif($order->status === 'shipped') Expédiée
                                    @elseif($order->status === 'delivered') Livrée
                                    @elseif($order->status === 'cancelled') Annulée
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Preview -->
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-4">
                            <!-- Product Images -->
                            <div class="flex -space-x-2 overflow-hidden">
                                @foreach($order->items->take(3) as $item)
                                    @if($item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="inline-block h-16 w-16 rounded-lg ring-2 ring-white object-cover">
                                    @else
                                        <div class="inline-block h-16 w-16 rounded-lg ring-2 ring-white bg-gray-200 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                @endforeach
                                @if($order->items->count() > 3)
                                    <div class="inline-block h-16 w-16 rounded-lg ring-2 ring-white bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">+{{ $order->items->count() - 3 }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Order Summary -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1 truncate">
                                    {{ $order->items->pluck('product.name')->take(2)->implode(', ') }}
                                    @if($order->items->count() > 2)
                                        <span class="text-gray-500">et {{ $order->items->count() - 2 }} autre{{ $order->items->count() - 2 > 1 ? 's' : '' }}</span>
                                    @endif
                                </p>

                                <!-- Delivery Address -->
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                    Voir détails
                                </a>

                                @if($order->status === 'shipped')
                                    <button type="button"
                                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition">
                                        Suivre colis
                                    </button>
                                @endif

                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none transition">
                                            Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Info -->
                        @if($order->payment)
                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    @if($order->payment->payment_method === 'cash_on_delivery')
                                        Paiement à la livraison
                                    @elseif($order->payment->payment_method === 'card')
                                        Carte bancaire
                                    @elseif($order->payment->payment_method === 'e_dinar')
                                        e-Dinar
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->payment->status === 'failed') bg-red-100 text-red-800
                                    @endif">
                                    @if($order->payment->status === 'pending') En attente
                                    @elseif($order->payment->status === 'processing') En cours
                                    @elseif($order->payment->status === 'completed') Payé
                                    @elseif($order->payment->status === 'failed') Échoué
                                    @elseif($order->payment->status === 'refunded') Remboursé
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-12 bg-white rounded-lg shadow-md">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune commande</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request('status'))
                    Vous n'avez aucune commande avec ce statut.
                @else
                    Vous n'avez pas encore passé de commande.
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition">
                    Découvrir nos produits
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
