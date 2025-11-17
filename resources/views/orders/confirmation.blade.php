@extends('layouts.frontend')

@section('title', 'Commande confirmée - Dropshipping TN')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Success Message -->
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Commande confirmée !</h1>
        <p class="text-lg text-gray-600">Merci pour votre commande</p>
    </div>

    <!-- Order Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between text-white">
                <div>
                    <p class="text-sm opacity-90">Numéro de commande</p>
                    <p class="text-2xl font-bold">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Date</p>
                    <p class="text-lg font-semibold">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Statut de la commande</p>
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
                <div class="text-right">
                    <p class="text-sm text-gray-600 mb-1">Mode de paiement</p>
                    <p class="font-medium text-gray-900">
                        @if($order->payment->payment_method === 'cash_on_delivery')
                            Paiement à la livraison
                        @elseif($order->payment->payment_method === 'card')
                            Carte bancaire
                        @elseif($order->payment->payment_method === 'e_dinar')
                            e-Dinar
                        @endif
                    </p>
                </div>
            </div>

            <!-- What's Next Section -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Prochaines étapes</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Vous recevrez un email de confirmation à <strong>{{ $order->user->email }}</strong></li>
                                <li>Les fournisseurs vont préparer vos articles</li>
                                <li>Vous serez notifié par SMS lors de l'expédition</li>
                                @if($order->payment->payment_method === 'cash_on_delivery')
                                    <li>Préparez <strong>{{ number_format($order->total_amount, 2) }} DT</strong> en espèces pour le livreur</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles commandés</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
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
                                    <p class="text-xs text-gray-500">{{ $item->getFormattedVariantAttributes(true) }}</p>
                                @endif
                                <p class="text-sm text-gray-500">Par {{ $item->supplier->business_name ?? $item->supplier->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">Quantité: {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($item->subtotal, 2) }} DT</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                    @if($item->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($item->status === 'accepted') bg-blue-100 text-blue-800
                                    @elseif($item->status === 'processing') bg-indigo-100 text-indigo-800
                                    @elseif($item->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($item->status === 'delivered') bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Addresses -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Shipping Address -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Adresse de livraison</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                        <p class="font-medium text-gray-900">{{ $order->shippingAddress->full_name }}</p>
                        <p class="text-gray-600 mt-1">{{ $order->shippingAddress->phone }}</p>
                        <p class="text-gray-600 mt-1">{{ $order->shippingAddress->address_line1 }}</p>
                        @if($order->shippingAddress->address_line2)
                            <p class="text-gray-600">{{ $order->shippingAddress->address_line2 }}</p>
                        @endif
                        <p class="text-gray-600">
                            {{ $order->shippingAddress->postal_code }} {{ $order->shippingAddress->city }}
                        </p>
                        <p class="text-gray-600">{{ $order->shippingAddress->state }}, Tunisie</p>
                    </div>
                </div>

                <!-- Billing Address -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Adresse de facturation</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                        <p class="font-medium text-gray-900">{{ $order->billingAddress->full_name }}</p>
                        <p class="text-gray-600 mt-1">{{ $order->billingAddress->phone }}</p>
                        <p class="text-gray-600 mt-1">{{ $order->billingAddress->address_line1 }}</p>
                        @if($order->billingAddress->address_line2)
                            <p class="text-gray-600">{{ $order->billingAddress->address_line2 }}</p>
                        @endif
                        <p class="text-gray-600">
                            {{ $order->billingAddress->postal_code }} {{ $order->billingAddress->city }}
                        </p>
                        <p class="text-gray-600">{{ $order->billingAddress->state }}, Tunisie</p>
                    </div>
                </div>
            </div>

            <!-- Order Total -->
            <div class="border-t border-gray-200 pt-4">
                <div class="space-y-2">
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
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} DT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('orders.show', $order) }}"
           class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            Suivre ma commande
        </a>
        <a href="{{ route('products.index') }}"
           class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            Continuer mes achats
        </a>
    </div>

    <!-- Help Section -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Besoin d'aide ?
            <a href="mailto:support@dropshipping.tn" class="text-indigo-600 hover:text-indigo-700 font-medium">
                Contactez notre support
            </a>
        </p>
    </div>
</div>
@endsection
