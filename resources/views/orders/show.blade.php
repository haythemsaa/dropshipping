@extends('layouts.frontend')

@section('title', 'Commande ' . $order->order_number . ' - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Accueil
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('orders.index') }}" class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">Mes commandes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">{{ $order->order_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm opacity-90">Commande</p>
                            <p class="text-2xl font-bold">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm opacity-90">{{ $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-lg">{{ $order->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Statut de la commande</span>
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

            <!-- Order Progress -->
            @if(!in_array($order->status, ['cancelled']))
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Suivi de commande</h3>

                    <div class="relative">
                        <!-- Progress Line -->
                        <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200"></div>

                        <div class="space-y-6">
                            <!-- Order Placed -->
                            <div class="relative flex items-start">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-500 border-4 border-white relative z-10">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Commande passée</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>

                            <!-- Confirmed -->
                            <div class="relative flex items-start">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full @if(in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif border-4 border-white relative z-10">
                                    @if(in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']))
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium @if(in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'])) text-gray-900 @else text-gray-500 @endif">Commande confirmée</p>
                                    <p class="text-sm text-gray-500">En attente de confirmation</p>
                                </div>
                            </div>

                            <!-- Processing -->
                            <div class="relative flex items-start">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif border-4 border-white relative z-10">
                                    @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) text-gray-900 @else text-gray-500 @endif">En préparation</p>
                                    <p class="text-sm text-gray-500">Préparation par les fournisseurs</p>
                                </div>
                            </div>

                            <!-- Shipped -->
                            <div class="relative flex items-start">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full @if(in_array($order->status, ['shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif border-4 border-white relative z-10">
                                    @if(in_array($order->status, ['shipped', 'delivered']))
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium @if(in_array($order->status, ['shipped', 'delivered'])) text-gray-900 @else text-gray-500 @endif">Expédiée</p>
                                    <p class="text-sm text-gray-500">En cours de livraison</p>
                                </div>
                            </div>

                            <!-- Delivered -->
                            <div class="relative flex items-start">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full @if($order->status === 'delivered') bg-green-500 @else bg-gray-300 @endif border-4 border-white relative z-10">
                                    @if($order->status === 'delivered')
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium @if($order->status === 'delivered') text-gray-900 @else text-gray-500 @endif">Livrée</p>
                                    <p class="text-sm text-gray-500">Commande reçue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                <span class="text-sm font-medium text-gray-900">{{ $supplier->business_name ?? $supplier->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach($items as $item)
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
                                         class="w-24 h-24 object-cover rounded-lg">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900">{{ $item->getDisplayName() }}</h4>
                                    @if($item->variant_attributes)
                                        <p class="text-xs text-gray-500 mt-1">{{ $item->getFormattedVariantAttributes(true) }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">SKU: {{ $item->getSku() }}</p>
                                    <div class="mt-2 flex items-center">
                                        <p class="text-sm text-gray-600">
                                            Quantité: <span class="font-medium">{{ $item->quantity }}</span>
                                        </p>
                                        <span class="mx-2 text-gray-300">•</span>
                                        <p class="text-sm text-gray-600">
                                            Prix unitaire: <span class="font-medium">{{ number_format($item->unit_price, 2) }} DT</span>
                                        </p>
                                    </div>
                                    <div class="mt-2">
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

                                    <!-- Tracking Info -->
                                    @if($item->shipment)
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                            <div class="flex items-center text-sm text-blue-900">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                </svg>
                                                <span class="font-medium">{{ $item->shipment->carrier }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ $item->shipment->tracking_number }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($item->subtotal, 2) }} DT</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Order Notes -->
            @if($order->notes)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Notes de commande</h3>
                    <p class="text-gray-600 text-sm">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
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
                            <span class="text-gray-600">Frais de livraison</span>
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

            <!-- Payment Information -->
            @if($order->payment)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Mode de paiement</span>
                            <span class="font-medium text-gray-900">
                                @if($order->payment->payment_method === 'cash_on_delivery')
                                    COD
                                @elseif($order->payment->payment_method === 'card')
                                    Carte
                                @elseif($order->payment->payment_method === 'e_dinar')
                                    e-Dinar
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Statut</span>
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
                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler la commande
                        </button>
                    </form>
                </div>
            @endif

            <!-- Help -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Besoin d'aide ?</h3>
                <p class="text-sm text-blue-700 mb-3">Notre équipe est là pour vous aider</p>
                <a href="mailto:support@dropshipping.tn"
                   class="inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-800">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    support@dropshipping.tn
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
