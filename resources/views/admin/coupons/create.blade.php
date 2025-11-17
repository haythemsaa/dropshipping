@extends('layouts.dashboard')

@section('title', 'Créer un coupon')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.coupons.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Créer un nouveau coupon</h1>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <!-- Code and Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code du coupon *</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}"
                           placeholder="Ex: PROMO2024"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Le code sera converti en majuscules</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           placeholder="Ex: Promotion Black Friday"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Description du coupon (visible pour les clients)"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Type and Value -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type de réduction *</label>
                    <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateValueLabel()">
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Montant fixe</option>
                        <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>Livraison gratuite</option>
                    </select>
                </div>

                <div id="value-field">
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                        <span id="value-label">Valeur (%)</span> *
                    </label>
                    <input type="number" name="value" id="value" value="{{ old('value') }}" step="0.01" min="0"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    <p class="mt-1 text-xs text-gray-500" id="value-help">Entre 0 et 100</p>
                </div>
            </div>

            <!-- Conditions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-2">Montant minimum commande (TND)</label>
                    <input type="number" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount') }}"
                           step="0.01" min="0"
                           placeholder="Ex: 50.00"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Laisser vide si aucun minimum</p>
                </div>

                <div id="max-discount-field">
                    <label for="max_discount_amount" class="block text-sm font-medium text-gray-700 mb-2">Plafond de réduction (TND)</label>
                    <input type="number" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount') }}"
                           step="0.01" min="0"
                           placeholder="Ex: 100.00"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Pour les coupons pourcentage uniquement</p>
                </div>
            </div>

            <!-- Validity Period -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Date de début *</label>
                    <input type="datetime-local" name="valid_from" id="valid_from" value="{{ old('valid_from', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Date de fin *</label>
                    <input type="datetime-local" name="valid_until" id="valid_until" value="{{ old('valid_until') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                </div>
            </div>

            <!-- Usage Limits -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">Limite d'utilisation globale</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}"
                           min="1" placeholder="Illimité"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Laisser vide pour illimité</p>
                </div>

                <div>
                    <label for="usage_limit_per_user" class="block text-sm font-medium text-gray-700 mb-2">Limite par utilisateur *</label>
                    <input type="number" name="usage_limit_per_user" id="usage_limit_per_user" value="{{ old('usage_limit_per_user', 1) }}"
                           min="1" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Categories and Products (optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Restreindre à des catégories spécifiques (optionnel)</label>
                <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-md p-4">
                    @foreach($categories as $category)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                   {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2">{{ $category->name }}</span>
                        </label>
                        @if($category->children && $category->children->count() > 0)
                            <div class="ml-6">
                                @foreach($category->children as $child)
                                    <label class="flex items-center mb-2">
                                        <input type="checkbox" name="category_ids[]" value="{{ $child->id }}"
                                               {{ in_array($child->id, old('category_ids', [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm">{{ $child->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-500">Laisser vide pour appliquer à tous les produits</p>
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Activer le coupon immédiatement</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Créer le coupon
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateValueLabel() {
    const type = document.getElementById('type').value;
    const valueLabel = document.getElementById('value-label');
    const valueHelp = document.getElementById('value-help');
    const valueField = document.getElementById('value-field');
    const maxDiscountField = document.getElementById('max-discount-field');

    if (type === 'percentage') {
        valueLabel.textContent = 'Valeur (%)';
        valueHelp.textContent = 'Entre 0 et 100';
        valueField.style.display = 'block';
        maxDiscountField.style.display = 'block';
    } else if (type === 'fixed') {
        valueLabel.textContent = 'Montant (TND)';
        valueHelp.textContent = 'Montant de la réduction';
        valueField.style.display = 'block';
        maxDiscountField.style.display = 'none';
    } else {
        valueField.style.display = 'none';
        maxDiscountField.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateValueLabel);
</script>
@endsection
