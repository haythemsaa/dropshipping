@extends('layouts.frontend')

@section('title', 'Comparaison de produits - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-balance-scale text-purple-600 mr-2"></i>
            Comparaison de produits
        </h1>

        @if($products->count() > 0)
            <form action="{{ route('comparison.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">
                    <i class="fas fa-trash mr-1"></i> Vider la comparaison
                </button>
            </form>
        @endif
    </div>

    @if($products->count() < 2)
        <div class="text-center py-16 bg-gray-50 rounded-lg">
            <i class="fas fa-balance-scale text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun produit à comparer</h3>
            <p class="text-gray-600 mb-6">
                Ajoutez au moins 2 produits pour pouvoir les comparer
            </p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                Parcourir les produits
            </a>
        </div>
    @else
        <!-- Comparison Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-x-auto">
            <table class="w-full">
                <!-- Product Images & Names -->
                <thead>
                    <tr class="border-b">
                        <th class="w-48 p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            Produits
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center align-top min-w-[250px]">
                                <div class="relative">
                                    <button onclick="removeProduct({{ $product->id }})"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>

                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <div class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden">
                                            @if($product->images->first())
                                                <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover hover:scale-105 transition">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-300 text-4xl"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <h3 class="font-semibold text-gray-900 hover:text-purple-600 line-clamp-2 mb-2">
                                            {{ $product->name }}
                                        </h3>
                                    </a>

                                    <p class="text-sm text-gray-500 mb-3">{{ $product->supplier->business_name }}</p>

                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="block bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition">
                                        Voir le produit
                                    </a>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    <!-- Prix -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            <i class="fas fa-tag text-purple-600 mr-2"></i> Prix
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ number_format($product->getActivePrice(), 2) }} DT
                                </span>
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <div class="text-sm text-gray-400 line-through mt-1">
                                        {{ number_format($product->compare_at_price, 2) }} DT
                                    </div>
                                    <div class="text-xs text-green-600 font-semibold mt-1">
                                        -{{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- Stock -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            <i class="fas fa-box text-purple-600 mr-2"></i> Disponibilité
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                @if($product->getTotalStock() > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        En stock ({{ $product->getTotalStock() }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Rupture de stock
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- Catégorie -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            <i class="fas fa-folder text-purple-600 mr-2"></i> Catégorie
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                <a href="{{ route('products.category', $product->category->slug) }}"
                                   class="text-purple-600 hover:text-purple-700">
                                    {{ $product->category->name }}
                                </a>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Évaluations -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            <i class="fas fa-star text-purple-600 mr-2"></i> Évaluations
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                @if($product->reviews_count > 0)
                                    <div class="flex items-center justify-center mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product->average_rating))
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @elseif($i - 0.5 <= $product->average_rating)
                                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                                            @else
                                                <i class="far fa-star text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ number_format($product->average_rating, 1) }} / 5 ({{ $product->reviews_count }} avis)
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Aucun avis</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- Description -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0 align-top">
                            <i class="fas fa-align-left text-purple-600 mr-2"></i> Description
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 align-top">
                                <div class="text-sm text-gray-700 line-clamp-4 text-left">
                                    {{ Str::limit($product->description, 200) }}
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <!-- SKU -->
                    <tr class="border-b hover:bg-gray-50">
                        <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0">
                            <i class="fas fa-barcode text-purple-600 mr-2"></i> SKU
                        </th>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                <span class="font-mono text-sm text-gray-600">{{ $product->sku }}</span>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Variantes -->
                    @if($products->filter(fn($p) => $p->hasVariants())->count() > 0)
                        <tr class="border-b hover:bg-gray-50">
                            <th class="p-4 text-left bg-gray-50 font-semibold text-gray-700 sticky left-0 align-top">
                                <i class="fas fa-palette text-purple-600 mr-2"></i> Variantes
                            </th>
                            @foreach($products as $product)
                                <td class="p-4 align-top">
                                    @if($product->hasVariants())
                                        <div class="text-sm text-left">
                                            @php
                                                $attributes = $product->variants()
                                                    ->where('is_active', true)
                                                    ->get()
                                                    ->pluck('attributes')
                                                    ->flatten(1)
                                                    ->unique()
                                                    ->groupBy(fn($attr) => array_keys($attr)[0]);
                                            @endphp
                                            @foreach($attributes as $name => $values)
                                                <div class="mb-2">
                                                    <span class="font-semibold text-gray-700">{{ ucfirst($name) }}:</span>
                                                    <span class="text-gray-600">
                                                        {{ $values->pluck(fn($v) => $v[array_keys($v)[0]])->unique()->join(', ') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Aucune variante</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Add more products -->
        <div class="mt-8 text-center">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold">
                <i class="fas fa-plus-circle mr-2"></i>
                Ajouter d'autres produits à comparer
            </a>
        </div>
    @endif
</div>

<script>
function removeProduct(productId) {
    if (confirm('Voulez-vous retirer ce produit de la comparaison ?')) {
        fetch(`/comparaison/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}
</script>
@endsection
