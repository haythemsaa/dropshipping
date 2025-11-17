@extends('layouts.dashboard')

@section('title', 'Variantes du produit : ' . $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supplier.products.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Variantes du produit</h1>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $product->name }} - SKU: {{ $product->sku }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="showBulkModal = true"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Générer en masse
                </button>
                <a href="{{ route('supplier.products.variants.create', $product) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle Variante
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total variantes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $variants->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Variantes actives</p>
                    <p class="text-2xl font-bold text-green-600">{{ $variants->where('is_active', true)->count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">En stock</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $variants->where('stock_quantity', '>', 0)->count() }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Stock total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $variants->sum('stock_quantity') }}</p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des variantes -->
    <div class="bg-white rounded-lg shadow overflow-hidden" x-data="{
        showBulkModal: false,
        selectedAttributes: {}
    }">
        @if($variants->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Variante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SKU
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($variants as $variant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($variant->image_path)
                                            <img src="{{ Storage::url($variant->image_path) }}"
                                                 alt="{{ $variant->getFormattedAttributes() }}"
                                                 class="w-12 h-12 object-cover rounded mr-3">
                                        @elseif($product->images->first())
                                            <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-12 h-12 object-cover rounded mr-3 opacity-50">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $variant->getFormattedAttributes() }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $variant->getFormattedAttributes(true) }}
                                            </div>
                                            @if($variant->is_default)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                    Par défaut
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">{{ $variant->sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($variant->price, 2) }} TND</div>
                                    @if($variant->compare_at_price)
                                        <div class="text-xs text-gray-500 line-through">{{ number_format($variant->compare_at_price, 2) }} TND</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($variant->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $variant->stock_quantity }} unités
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rupture
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('supplier.products.variants.toggle-status', [$product, $variant]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit">
                                            @if($variant->is_active)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    Inactif
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('supplier.products.variants.edit', [$product, $variant]) }}"
                                           class="text-indigo-600 hover:text-indigo-900"
                                           title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('supplier.products.variants.destroy', [$product, $variant]) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette variante ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune variante</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer des variantes pour ce produit.</p>
                <div class="mt-6 flex items-center justify-center space-x-3">
                    <button @click="showBulkModal = true"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        Générer automatiquement
                    </button>
                    <a href="{{ route('supplier.products.variants.create', $product) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer manuellement
                    </a>
                </div>
            </div>
        @endif

        <!-- Modale Génération en masse -->
        <div x-show="showBulkModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showBulkModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showBulkModal = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showBulkModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <form action="{{ route('supplier.products.variants.generate-bulk', $product) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Génération automatique de variantes</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Sélectionnez les valeurs pour chaque attribut. Toutes les combinaisons possibles seront créées automatiquement.
                            </p>

                            <div class="space-y-6">
                                @forelse($attributes as $attribute)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <label class="block text-sm font-medium text-gray-900 mb-3">
                                            {{ $attribute->name }}
                                        </label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            @foreach($attribute->activeValues as $value)
                                                <label class="flex items-center p-2 border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                                    <input type="checkbox"
                                                           name="attributes[{{ $attribute->id }}][]"
                                                           value="{{ $value->id }}"
                                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                                                        @if($value->hasColor())
                                                            <span class="w-4 h-4 rounded-full mr-1 border border-gray-300"
                                                                  style="background-color: {{ $value->color_code }}"></span>
                                                        @endif
                                                        {{ $value->value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-500">Aucun attribut disponible.</p>
                                        <p class="text-xs text-gray-400 mt-1">Contactez l'administrateur pour créer des attributs.</p>
                                    </div>
                                @endforelse

                                @if($attributes->count() > 0)
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                        <div>
                                            <label for="sku_prefix" class="block text-sm font-medium text-gray-700">Préfixe SKU *</label>
                                            <input type="text" name="sku_prefix" id="sku_prefix" required
                                                   value="{{ $product->sku }}"
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <p class="mt-1 text-xs text-gray-500">Ex: PROD-001 générera PROD-001-ROU-M, PROD-001-BLE-L, etc.</p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="base_price" class="block text-sm font-medium text-gray-700">Prix de base *</label>
                                                <input type="number" name="base_price" id="base_price" required step="0.01" min="0"
                                                       value="{{ $product->price }}"
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                            <div>
                                                <label for="base_stock" class="block text-sm font-medium text-gray-700">Stock de base *</label>
                                                <input type="number" name="base_stock" id="base_stock" required min="0"
                                                       value="10"
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    @if($attributes->count() == 0) disabled @endif
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-300 disabled:cursor-not-allowed sm:ml-3 sm:w-auto sm:text-sm">
                                Générer les variantes
                            </button>
                            <button type="button" @click="showBulkModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
