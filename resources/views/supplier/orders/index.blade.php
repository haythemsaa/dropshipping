@extends('layouts.dashboard')

@section('title', 'Mes commandes - Fournisseur')

@section('sidebar')
<aside class="w-64 bg-white shadow-md">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900">Espace Fournisseur</h2>
        <p class="text-sm text-gray-600">{{ Auth::user()->business_name ?? Auth::user()->name }}</p>
    </div>
    <nav class="mt-6">
        <a href="{{ route('supplier.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>
        <a href="{{ route('supplier.products.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Produits
        </a>
        <a href="{{ route('supplier.orders.index') }}" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Commandes
        </a>
        <a href="{{ route('supplier.commissions') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
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
        <h1 class="text-2xl font-bold text-gray-900">Mes commandes</h1>
        <p class="text-sm text-gray-600 mt-1">Gérez vos commandes et expéditions</p>
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Acceptées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $acceptedCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Expédiées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $shippedCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Livrées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $deliveredCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('supplier.orders.index') }}"
               class="@if(!request('status')) border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Toutes
            </a>
            <a href="{{ route('supplier.orders.index', ['status' => 'pending']) }}"
               class="@if(request('status') === 'pending') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                En attente
            </a>
            <a href="{{ route('supplier.orders.index', ['status' => 'accepted']) }}"
               class="@if(request('status') === 'accepted') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Acceptées
            </a>
            <a href="{{ route('supplier.orders.index', ['status' => 'processing']) }}"
               class="@if(request('status') === 'processing') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                En préparation
            </a>
            <a href="{{ route('supplier.orders.index', ['status' => 'shipped']) }}"
               class="@if(request('status') === 'shipped') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Expédiées
            </a>
        </nav>
    </div>

    <!-- Orders List -->
    @if($orderItems->count() > 0)
        <div class="space-y-4">
            @foreach($orderItems as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Commande #{{ $item->order->order_number }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($item->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($item->status === 'accepted') bg-blue-100 text-blue-800
                                        @elseif($item->status === 'processing') bg-indigo-100 text-indigo-800
                                        @elseif($item->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($item->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($item->status === 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $item->order->created_at->format('d/m/Y à H:i') }} •
                                    Client: {{ $item->order->user->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ number_format($item->subtotal, 2) }} DT</p>
                                <p class="text-sm text-gray-600">Votre montant: {{ number_format($item->supplier_amount, 2) }} DT</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 py-4 border-t border-gray-200">
                            @php
                                $imageUrl = null;
                                if ($item->variant && $item->variant->image_path) {
                                    $imageUrl = Storage::url($item->variant->image_path);
                                } elseif ($item->product->images->first()) {
                                    $imageUrl = Storage::url($item->product->images->first()->image_path);
                                }
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $item->getDisplayName() }}"
                                     class="w-20 h-20 object-cover rounded">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $item->getDisplayName() }}</h4>
                                @if($item->variant_attributes)
                                    <p class="text-xs text-gray-500 mt-1">{{ $item->getFormattedVariantAttributes(true) }}</p>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">SKU: {{ $item->getSku() }}</p>
                                <p class="text-sm text-gray-600">Quantité: {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT</p>

                                @if($item->shipment)
                                    <div class="mt-2 flex items-center text-sm text-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        {{ $item->shipment->carrier }} - {{ $item->shipment->tracking_number }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col space-y-2">
                                @if($item->status === 'pending')
                                    <form action="{{ route('supplier.orders.accept', $item) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm transition">
                                            Accepter
                                        </button>
                                    </form>
                                    <form action="{{ route('supplier.orders.reject', $item) }}" method="POST"
                                          onsubmit="return confirm('Rejeter cette commande ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 text-sm transition">
                                            Rejeter
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($item->status, ['accepted', 'processing']) && !$item->shipment)
                                    <a href="{{ route('supplier.shipments.create', ['order_item' => $item->id]) }}"
                                       class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm transition">
                                        Créer expédition
                                    </a>
                                @endif

                                <a href="{{ route('supplier.orders.show', $item->order) }}"
                                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm transition">
                                    Voir détails
                                </a>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-start text-sm">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->order->shippingAddress->full_name }}</p>
                                    <p class="text-gray-600">
                                        {{ $item->order->shippingAddress->address_line1 }},
                                        {{ $item->order->shippingAddress->city }},
                                        {{ $item->order->shippingAddress->state }}
                                    </p>
                                    <p class="text-gray-600">{{ $item->order->shippingAddress->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orderItems->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune commande</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request('status'))
                    Aucune commande avec ce statut.
                @else
                    Vous n'avez pas encore reçu de commande.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
