@extends('layouts.dashboard')

@section('title', 'Détails commande - Admin')

@section('sidebar')
<aside class="w-64 bg-white shadow-md">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900">Administration</h2>
        <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
    </div>
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Fournisseurs
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Catégories
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Produits
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Commandes
        </a>
        <a href="{{ route('admin.commissions.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
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
            <a href="{{ route('admin.orders.index') }}"
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
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Statut de la commande</p>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-600">Montant total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} DT</p>
                    </div>
                </div>
            </div>

            <!-- Order Items by Supplier -->
            @foreach($itemsBySupplier as $supplierId => $items)
                @php
                    $supplier = $items->first()->supplier;
                @endphp
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">
                                    Fournisseur: {{ $supplier->business_name ?? $supplier->name }}
                                </span>
                            </div>
                            <a href="{{ route('admin.suppliers.show', $supplier) }}"
                               class="text-sm text-indigo-600 hover:text-indigo-700">
                                Voir fournisseur →
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach($items as $item)
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
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
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $item->getDisplayName() }}</h4>
                                            @if($item->variant_attributes)
                                                <p class="text-xs text-gray-500 mt-1">{{ $item->getFormattedVariantAttributes(true) }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 mt-1">SKU: {{ $item->getSku() }}</p>
                                            <p class="text-sm text-gray-600">{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($item->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->status === 'accepted') bg-blue-100 text-blue-800
                                            @elseif($item->status === 'processing') bg-indigo-100 text-indigo-800
                                            @elseif($item->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($item->status === 'delivered') bg-green-100 text-green-800
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
                                            <p class="text-gray-600">Montant fournisseur</p>
                                            <p class="font-semibold text-green-600">{{ number_format($item->supplier_amount, 2) }} DT</p>
                                        </div>
                                    </div>

                                    @if($item->shipment)
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                            <div class="flex items-center text-sm text-blue-900">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                </svg>
                                                <span class="font-medium">{{ $item->shipment->carrier }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ $item->shipment->tracking_number }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

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
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations client</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nom</p>
                        <p class="text-base font-medium text-gray-900">{{ $order->user->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-base font-medium text-gray-900">{{ $order->user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sous-total</span>
                        <span class="font-medium text-gray-900">{{ number_format($order->subtotal, 2) }} DT</span>
                    </div>
                    @if($order->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Livraison</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->shipping_cost, 2) }} DT</span>
                        </div>
                    @endif
                    @if($order->tax_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">TVA</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->tax_amount, 2) }} DT</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-bold text-gray-900">Total</span>
                            <span class="text-base font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} DT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            @if($order->payment)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h3>

                    <div class="space-y-3 text-sm">
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

            <!-- Actions -->
            @if(in_array($order->status, ['pending', 'confirmed']))
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>

                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST"
                          onsubmit="return confirm('Annuler cette commande ? Le stock sera restauré.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                            Annuler la commande
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
