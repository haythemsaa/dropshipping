@extends('layouts.dashboard')

@section('title', 'Détails commande - Fournisseur')

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
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Commande #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <a href="{{ route('supplier.orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Your Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vos articles dans cette commande</h2>

                <div class="space-y-4">
                    @foreach($myItems as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start space-x-4">
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
                                         class="w-24 h-24 object-cover rounded">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $item->getDisplayName() }}</h3>
                                            @if($item->variant_attributes)
                                                <p class="text-xs text-gray-500 mt-1">{{ $item->getFormattedVariantAttributes(true) }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 mt-1">SKU: {{ $item->getSku() }}</p>
                                            <p class="text-sm text-gray-600">Quantité: {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT</p>
                                        </div>
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

                                    <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Sous-total</p>
                                            <p class="font-semibold text-gray-900">{{ number_format($item->subtotal, 2) }} DT</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Commission ({{ number_format($item->commission_rate, 2) }}%)</p>
                                            <p class="font-semibold text-gray-900">{{ number_format($item->commission_amount, 2) }} DT</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Votre montant</p>
                                            <p class="font-semibold text-green-600">{{ number_format($item->supplier_amount, 2) }} DT</p>
                                        </div>
                                    </div>

                                    @if($item->shipment)
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center text-sm text-blue-900">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                    </svg>
                                                    <span class="font-medium">{{ $item->shipment->carrier }}</span>
                                                    <span class="mx-2">•</span>
                                                    <span>{{ $item->shipment->tracking_number }}</span>
                                                </div>
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    @if($item->shipment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($item->shipment->status === 'in_transit') bg-blue-100 text-blue-800
                                                    @elseif($item->shipment->status === 'delivered') bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst($item->shipment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="mt-4 flex items-center space-x-2">
                                        @if($item->status === 'pending')
                                            <form action="{{ route('supplier.orders.accept', $item) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm transition">
                                                    Accepter
                                                </button>
                                            </form>
                                            <form action="{{ route('supplier.orders.reject', $item) }}" method="POST"
                                                  onsubmit="return confirm('Rejeter cet article ?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 text-sm transition">
                                                    Rejeter
                                                </button>
                                            </form>
                                        @endif

                                        @if(in_array($item->status, ['accepted', 'processing']) && !$item->shipment)
                                            <a href="{{ route('supplier.shipments.create', ['order_item' => $item->id]) }}"
                                               class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm transition">
                                                Créer expédition
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="text-center">
                            <p class="text-gray-600">Total articles</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($myItems->sum('subtotal'), 2) }} DT</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600">Commission totale</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($myItems->sum('commission_amount'), 2) }} DT</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600">Votre total</p>
                            <p class="text-xl font-bold text-green-600">{{ number_format($myItems->sum('supplier_amount'), 2) }} DT</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Notes du client</h3>
                    <p class="text-gray-600 text-sm">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations commande</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Numéro</span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date</span>
                        <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Statut global</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total commande</span>
                            <span class="font-bold">{{ number_format($order->total_amount, 2) }} DT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations client</h3>

                <div class="space-y-2 text-sm">
                    <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                    <p class="text-gray-600">{{ $order->user->email }}</p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Adresse de livraison</h3>

                <div class="text-sm space-y-1">
                    <p class="font-medium text-gray-900">{{ $order->shippingAddress->full_name }}</p>
                    <p class="text-gray-600">{{ $order->shippingAddress->phone }}</p>
                    <p class="text-gray-600">{{ $order->shippingAddress->address_line1 }}</p>
                    @if($order->shippingAddress->address_line2)
                        <p class="text-gray-600">{{ $order->shippingAddress->address_line2 }}</p>
                    @endif
                    <p class="text-gray-600">
                        {{ $order->shippingAddress->postal_code }} {{ $order->shippingAddress->city }}
                    </p>
                    <p class="text-gray-600">{{ $order->shippingAddress->state }}, Tunisie</p>
                </div>
            </div>

            <!-- Payment -->
            @if($order->payment)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mode</span>
                            <span class="font-medium">
                                @if($order->payment->payment_method === 'cash_on_delivery') COD
                                @elseif($order->payment->payment_method === 'card') Carte
                                @elseif($order->payment->payment_method === 'e_dinar') e-Dinar
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Statut</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($order->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->payment->status === 'completed') bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
