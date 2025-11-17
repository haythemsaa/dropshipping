<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-clock-rotate-left text-purple-600 mr-2"></i>
            Produits Récemment Consultés
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            <div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Product Image -->
                <div class="relative aspect-square overflow-hidden bg-gray-100">
                    <a href="{{ route('products.show', $product->slug) }}">
                        @if($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-6xl"></i>
                            </div>
                        @endif
                    </a>

                    <!-- Stock Badge -->
                    @if($product->stock_quantity <= 0)
                        <span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-semibold">
                            Rupture de stock
                        </span>
                    @elseif($product->stock_quantity <= 10)
                        <span class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded-md text-xs font-semibold">
                            Stock limité
                        </span>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-purple-600 transition mb-2">
                            {{ $product->name }}
                        </h3>
                    </a>

                    <!-- Rating -->
                    @if($product->reviews_count > 0)
                        <div class="flex items-center mb-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($product->average_rating))
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    @elseif($i - 0.5 <= $product->average_rating)
                                        <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                    @else
                                        <i class="far fa-star text-gray-300 text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                        </div>
                    @endif

                    <!-- Price -->
                    <div class="flex items-baseline justify-between mb-3">
                        <div>
                            <span class="text-lg font-bold text-purple-600">{{ number_format($product->price, 2) }} DT</span>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <span class="text-xs text-gray-400 line-through ml-1">{{ number_format($product->compare_at_price, 2) }} DT</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('products.show', $product->slug) }}"
                       class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 rounded-md text-sm font-medium transition">
                        Voir le produit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
