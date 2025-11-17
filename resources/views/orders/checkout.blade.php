@extends('layouts.frontend')

@section('title', 'Finaliser la commande - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Finaliser votre commande</h1>
        <p class="mt-2 text-sm text-gray-600">Veuillez remplir vos informations de livraison et de paiement</p>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left column: Forms -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Adresse de livraison -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <svg class="inline-block w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Adresse de livraison
                    </h2>

                    @if(Auth::user()->addresses()->where('type', 'shipping')->exists())
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse existante</label>
                            <select name="shipping_address_id" id="shipping-address-select"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Nouvelle adresse --</option>
                                @foreach(Auth::user()->addresses()->where('type', 'shipping')->get() as $address)
                                    <option value="{{ $address->id }}"
                                            data-address="{{ json_encode($address) }}">
                                        {{ $address->full_name }} - {{ $address->address_line1 }}, {{ $address->city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div id="shipping-address-form" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_full_name" class="block text-sm font-medium text-gray-700">Nom complet *</label>
                                <input type="text" name="shipping_full_name" id="shipping_full_name" required
                                       value="{{ old('shipping_full_name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('shipping_full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                                <input type="tel" name="shipping_phone" id="shipping_phone" required
                                       value="{{ old('shipping_phone') }}"
                                       placeholder="+216 XX XXX XXX"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('shipping_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="shipping_address_line1" class="block text-sm font-medium text-gray-700">Adresse ligne 1 *</label>
                            <input type="text" name="shipping_address_line1" id="shipping_address_line1" required
                                   value="{{ old('shipping_address_line1') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('shipping_address_line1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address_line2" class="block text-sm font-medium text-gray-700">Adresse ligne 2</label>
                            <input type="text" name="shipping_address_line2" id="shipping_address_line2"
                                   value="{{ old('shipping_address_line2') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700">Ville *</label>
                                <input type="text" name="shipping_city" id="shipping_city" required
                                       value="{{ old('shipping_city') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('shipping_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700">Gouvernorat *</label>
                                <select name="shipping_state" id="shipping_state" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Tunis" {{ old('shipping_state') == 'Tunis' ? 'selected' : '' }}>Tunis</option>
                                    <option value="Ariana" {{ old('shipping_state') == 'Ariana' ? 'selected' : '' }}>Ariana</option>
                                    <option value="Ben Arous" {{ old('shipping_state') == 'Ben Arous' ? 'selected' : '' }}>Ben Arous</option>
                                    <option value="Manouba" {{ old('shipping_state') == 'Manouba' ? 'selected' : '' }}>Manouba</option>
                                    <option value="Nabeul" {{ old('shipping_state') == 'Nabeul' ? 'selected' : '' }}>Nabeul</option>
                                    <option value="Zaghouan" {{ old('shipping_state') == 'Zaghouan' ? 'selected' : '' }}>Zaghouan</option>
                                    <option value="Bizerte" {{ old('shipping_state') == 'Bizerte' ? 'selected' : '' }}>Bizerte</option>
                                    <option value="Béja" {{ old('shipping_state') == 'Béja' ? 'selected' : '' }}>Béja</option>
                                    <option value="Jendouba" {{ old('shipping_state') == 'Jendouba' ? 'selected' : '' }}>Jendouba</option>
                                    <option value="Kef" {{ old('shipping_state') == 'Kef' ? 'selected' : '' }}>Kef</option>
                                    <option value="Siliana" {{ old('shipping_state') == 'Siliana' ? 'selected' : '' }}>Siliana</option>
                                    <option value="Sousse" {{ old('shipping_state') == 'Sousse' ? 'selected' : '' }}>Sousse</option>
                                    <option value="Monastir" {{ old('shipping_state') == 'Monastir' ? 'selected' : '' }}>Monastir</option>
                                    <option value="Mahdia" {{ old('shipping_state') == 'Mahdia' ? 'selected' : '' }}>Mahdia</option>
                                    <option value="Sfax" {{ old('shipping_state') == 'Sfax' ? 'selected' : '' }}>Sfax</option>
                                    <option value="Kairouan" {{ old('shipping_state') == 'Kairouan' ? 'selected' : '' }}>Kairouan</option>
                                    <option value="Kasserine" {{ old('shipping_state') == 'Kasserine' ? 'selected' : '' }}>Kasserine</option>
                                    <option value="Sidi Bouzid" {{ old('shipping_state') == 'Sidi Bouzid' ? 'selected' : '' }}>Sidi Bouzid</option>
                                    <option value="Gabès" {{ old('shipping_state') == 'Gabès' ? 'selected' : '' }}>Gabès</option>
                                    <option value="Médenine" {{ old('shipping_state') == 'Médenine' ? 'selected' : '' }}>Médenine</option>
                                    <option value="Tataouine" {{ old('shipping_state') == 'Tataouine' ? 'selected' : '' }}>Tataouine</option>
                                    <option value="Gafsa" {{ old('shipping_state') == 'Gafsa' ? 'selected' : '' }}>Gafsa</option>
                                    <option value="Tozeur" {{ old('shipping_state') == 'Tozeur' ? 'selected' : '' }}>Tozeur</option>
                                    <option value="Kebili" {{ old('shipping_state') == 'Kebili' ? 'selected' : '' }}>Kebili</option>
                                </select>
                                @error('shipping_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700">Code postal *</label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" required
                                       value="{{ old('shipping_postal_code') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse de facturation -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <svg class="inline-block w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Adresse de facturation
                    </h2>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="same_as_shipping" id="same-as-shipping" checked
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Identique à l'adresse de livraison</span>
                        </label>
                    </div>

                    <div id="billing-address-form" class="space-y-4 hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="billing_full_name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                <input type="text" name="billing_full_name" id="billing_full_name"
                                       value="{{ old('billing_full_name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="billing_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" name="billing_phone" id="billing_phone"
                                       value="{{ old('billing_phone') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="billing_address_line1" class="block text-sm font-medium text-gray-700">Adresse ligne 1</label>
                            <input type="text" name="billing_address_line1" id="billing_address_line1"
                                   value="{{ old('billing_address_line1') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="billing_address_line2" class="block text-sm font-medium text-gray-700">Adresse ligne 2</label>
                            <input type="text" name="billing_address_line2" id="billing_address_line2"
                                   value="{{ old('billing_address_line2') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="billing_city" class="block text-sm font-medium text-gray-700">Ville</label>
                                <input type="text" name="billing_city" id="billing_city"
                                       value="{{ old('billing_city') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="billing_state" class="block text-sm font-medium text-gray-700">Gouvernorat</label>
                                <select name="billing_state" id="billing_state"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Tunis">Tunis</option>
                                    <option value="Ariana">Ariana</option>
                                    <option value="Ben Arous">Ben Arous</option>
                                    <option value="Manouba">Manouba</option>
                                    <option value="Nabeul">Nabeul</option>
                                    <!-- Other governorates same as shipping -->
                                </select>
                            </div>

                            <div>
                                <label for="billing_postal_code" class="block text-sm font-medium text-gray-700">Code postal</label>
                                <input type="text" name="billing_postal_code" id="billing_postal_code"
                                       value="{{ old('billing_postal_code') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode de paiement -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <svg class="inline-block w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Mode de paiement
                    </h2>

                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked
                                   class="text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Paiement à la livraison (COD)</span>
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Payez en espèces à la réception de votre commande</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition opacity-50">
                            <input type="radio" name="payment_method" value="card" disabled
                                   class="text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Carte bancaire</span>
                                    <div class="flex space-x-1">
                                        <svg class="w-8 h-5" viewBox="0 0 48 32" fill="none">
                                            <rect width="48" height="32" rx="4" fill="#1434CB"/>
                                        </svg>
                                        <svg class="w-8 h-5" viewBox="0 0 48 32" fill="none">
                                            <rect width="48" height="32" rx="4" fill="#EB001B"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Visa, MasterCard (Prochainement disponible)</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition opacity-50">
                            <input type="radio" name="payment_method" value="e_dinar" disabled
                                   class="text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">e-Dinar</span>
                                    <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Paiement via D17 (Prochainement disponible)</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes de commande -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes de commande (optionnel)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Instructions spéciales pour la livraison...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Récapitulatif</h2>

                    <div class="space-y-3 mb-4">
                        @foreach($cart->items as $item)
                            <div class="flex items-center space-x-3">
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
                                         class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->getDisplayName() }}</p>
                                    @if($item->variant_id && $item->variant)
                                        <p class="text-xs text-gray-500">{{ $item->variant->getFormattedAttributes() }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">Qté: {{ $item->quantity }}</p>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ number_format($item->price * $item->quantity, 2) }} DT</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Code promo -->
                    <div class="border-t border-gray-200 pt-4 mb-4" x-data="{
                        code: '',
                        validating: false,
                        applied: false,
                        discount: 0,
                        type: '',
                        message: '',
                        error: false
                    }">
                        <label for="coupon_code" class="block text-sm font-medium text-gray-700 mb-2">Code promo</label>
                        <div class="flex gap-2">
                            <input type="text"
                                   x-model="code"
                                   name="coupon_code"
                                   id="coupon_code"
                                   placeholder="Entrez votre code"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase"
                                   :disabled="applied">
                            <button type="button"
                                    @click="validateCoupon()"
                                    :disabled="!code || validating || applied"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-sm font-medium transition">
                                <span x-show="!validating">Appliquer</span>
                                <span x-show="validating">...</span>
                            </button>
                            <button type="button"
                                    x-show="applied"
                                    @click="removeCoupon()"
                                    class="px-3 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 text-sm font-medium transition">
                                ✕
                            </button>
                        </div>
                        <div x-show="message"
                             x-text="message"
                             :class="error ? 'text-red-600' : 'text-green-600'"
                             class="mt-2 text-sm font-medium"></div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sous-total</span>
                            <span class="font-medium text-gray-900" id="subtotal">{{ number_format($cart->items->sum('subtotal'), 2) }} DT</span>
                        </div>
                        <div class="flex justify-between text-sm" x-data x-show="$root.querySelector('[x-data]').discount > 0">
                            <span class="text-green-600">Réduction</span>
                            <span class="font-medium text-green-600" id="discount">-0.00 DT</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Frais de livraison</span>
                            <span class="font-medium text-gray-900" id="shipping">7.00 DT</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-base font-semibold text-gray-900" id="total">{{ number_format($cart->items->sum('subtotal') + 7.00, 2) }} DT</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full mt-6 bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition font-medium">
                        Confirmer la commande
                    </button>

                    <!-- Trust Badges -->
                    <div class="mt-6">
                        <x-trust-badges layout="vertical" class="border-t border-gray-200 pt-4" />
                    </div>

                    <p class="mt-4 text-xs text-gray-500 text-center">
                        En passant commande, vous acceptez nos
                        <a href="#" class="text-indigo-600 hover:text-indigo-700">conditions générales de vente</a>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Toggle billing address form
    document.getElementById('same-as-shipping').addEventListener('change', function() {
        const billingForm = document.getElementById('billing-address-form');
        if (this.checked) {
            billingForm.classList.add('hidden');
        } else {
            billingForm.classList.remove('hidden');
        }
    });

    // Auto-fill address from existing addresses
    const shippingSelect = document.getElementById('shipping-address-select');
    if (shippingSelect) {
        shippingSelect.addEventListener('change', function() {
            if (this.value) {
                const address = JSON.parse(this.options[this.selectedIndex].dataset.address);
                document.getElementById('shipping_full_name').value = address.full_name || '';
                document.getElementById('shipping_phone').value = address.phone || '';
                document.getElementById('shipping_address_line1').value = address.address_line1 || '';
                document.getElementById('shipping_address_line2').value = address.address_line2 || '';
                document.getElementById('shipping_city').value = address.city || '';
                document.getElementById('shipping_state').value = address.state || '';
                document.getElementById('shipping_postal_code').value = address.postal_code || '';
            }
        });
    }

    // Coupon validation and management
    const subtotalAmount = {{ $cart->items->sum('subtotal') }};
    const shippingFee = 7.00;

    window.validateCoupon = function() {
        const scope = this;
        scope.validating = true;
        scope.error = false;
        scope.message = '';

        fetch('{{ route('orders.validate-coupon') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                coupon_code: scope.code.toUpperCase(),
                subtotal: subtotalAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            scope.validating = false;

            if (data.valid) {
                scope.applied = true;
                scope.discount = data.discount;
                scope.type = data.type;
                scope.message = data.message;
                scope.error = false;
                updateTotals(scope.discount, scope.type);
            } else {
                scope.error = true;
                scope.message = data.message;
            }
        })
        .catch(error => {
            scope.validating = false;
            scope.error = true;
            scope.message = 'Erreur lors de la validation du code promo.';
            console.error('Error:', error);
        });
    };

    window.removeCoupon = function() {
        const scope = this;
        scope.code = '';
        scope.applied = false;
        scope.discount = 0;
        scope.type = '';
        scope.message = '';
        scope.error = false;
        updateTotals(0, '');

        // Clear the hidden input value
        document.getElementById('coupon_code').value = '';
    };

    function updateTotals(discountAmount, couponType) {
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const shippingEl = document.getElementById('shipping');
        const totalEl = document.getElementById('total');

        let shipping = shippingFee;

        // Apply free shipping if coupon type is free_shipping
        if (couponType === 'free_shipping') {
            shipping = 0;
        }

        const total = subtotalAmount - discountAmount + shipping;

        // Update display
        subtotalEl.textContent = subtotalAmount.toFixed(2) + ' DT';
        discountEl.textContent = '-' + discountAmount.toFixed(2) + ' DT';
        shippingEl.textContent = shipping.toFixed(2) + ' DT';
        totalEl.textContent = total.toFixed(2) + ' DT';
    }
</script>
@endpush
@endsection
