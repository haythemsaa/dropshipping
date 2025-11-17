@extends('layouts.dashboard')

@section('title', 'Créer une variante')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-2">
            <a href="{{ route('supplier.products.variants.index', $product) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Créer une variante</h1>
        </div>
        <p class="text-sm text-gray-600">
            Produit: {{ $product->name }} ({{ $product->sku }})
        </p>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Attributs -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Attributs de la variante</h2>

            @if($attributes->count() > 0)
                <div class="space-y-4">
                    @foreach($attributes as $attribute)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $attribute->name }} *
                            </label>

                            @if($attribute->display_type === 'color')
                                <!-- Color swatches -->
                                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                                    @foreach($attribute->activeValues as $value)
                                        <label class="flex flex-col items-center cursor-pointer group">
                                            <input type="radio"
                                                   name="attribute_values[]"
                                                   value="{{ $value->id }}"
                                                   class="sr-only peer"
                                                   required>
                                            <div class="w-10 h-10 rounded-full border-2 border-gray-300 peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-500 transition-all"
                                                 style="background-color: {{ $value->color_code }};"
                                                 title="{{ $value->value }}">
                                            </div>
                                            <span class="text-xs text-gray-600 mt-1 group-hover:text-gray-900">{{ $value->value }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($attribute->display_type === 'button')
                                <!-- Button selection -->
                                <div class="flex flex-wrap gap-2">
                                    @foreach($attribute->activeValues as $value)
                                        <label class="cursor-pointer">
                                            <input type="radio"
                                                   name="attribute_values[]"
                                                   value="{{ $value->id }}"
                                                   class="sr-only peer"
                                                   required>
                                            <div class="px-4 py-2 border-2 border-gray-300 rounded-md hover:border-gray-400 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                                <span class="text-sm font-medium">{{ $value->value }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <!-- Select dropdown -->
                                <select name="attribute_values[]" required
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Sélectionner {{ strtolower($attribute->name) }} --</option>
                                    @foreach($attribute->activeValues as $value)
                                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun attribut disponible</h3>
                    <p class="mt-1 text-sm text-gray-500">Contactez l'administrateur pour créer des attributs.</p>
                </div>
            @endif
        </div>

        <!-- Informations de base -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informations de base</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU *</label>
                    <input type="text" name="sku" id="sku" required
                           value="{{ old('sku') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="{{ $product->sku }}-VAR-001">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Le SKU doit être unique pour chaque variante</p>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Prix (TND) *</label>
                    <input type="number" name="price" id="price" required step="0.01" min="0"
                           value="{{ old('price', $product->price) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="compare_at_price" class="block text-sm font-medium text-gray-700">Prix comparé (TND)</label>
                    <input type="number" name="compare_at_price" id="compare_at_price" step="0.01" min="0"
                           value="{{ old('compare_at_price') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('compare_at_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Prix barré pour afficher une promotion</p>
                </div>

                <div class="md:col-span-2">
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Quantité en stock *</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" required min="0"
                           value="{{ old('stock_quantity', 10) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Image de la variante</h2>

            <div class="space-y-4">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image spécifique (optionnel)</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                  cursor-pointer">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Si non fournie, l'image du produit principal sera utilisée. Max 2 MB.</p>
                </div>

                <div id="image-preview" class="hidden">
                    <img src="" alt="Prévisualisation" class="w-32 h-32 object-cover rounded-lg">
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Options</h2>

            <div class="space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           {{ old('is_default') ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">
                        Définir comme variante par défaut
                    </label>
                </div>
                <p class="ml-6 text-xs text-gray-500">La variante par défaut sera présélectionnée sur la page produit</p>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Variante active
                    </label>
                </div>
                <p class="ml-6 text-xs text-gray-500">Les variantes inactives ne seront pas visibles sur la boutique</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('supplier.products.variants.index', $product) }}"
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Annuler
            </a>
            <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Créer la variante
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
