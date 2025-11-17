@extends('layouts.frontend')

@section('title', $product->name . ' - Dropshipping TN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Accueil</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-600">Produits</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-indigo-600">
            {{ $product->category->name }}
        </a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div>
            <div class="bg-gray-100 rounded-lg overflow-hidden mb-4">
                @if($product->images->count() > 0)
                    <img src="{{ Storage::url($product->images->where('is_primary', true)->first()->image_path ?? $product->images->first()->image_path) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-contain"
                         id="main-image">
                @else
                    <div class="flex items-center justify-center h-96 text-gray-400">
                        <svg class="h-32 w-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                        <button onclick="changeImage('{{ Storage::url($image->image_path) }}')"
                                class="bg-gray-100 rounded-lg overflow-hidden hover:ring-2 hover:ring-indigo-600 transition">
                            <img src="{{ Storage::url($image->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-20 object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div x-data="{
            @if($product->hasVariants())
                selectedVariant: null,
                selectedAttributes: {},
                variants: {{ $product->variants()->where('is_active', true)->with('product')->get()->toJson() }},
                productAttributes: {{ \App\Models\ProductAttribute::active()->with('activeValues')->ordered()->get()->map(function($attr) use ($product) {
                    $valueIds = $product->variants()->where('is_active', true)->pluck('attribute_value_ids')->flatten()->unique()->toArray();
                    $attr->setRelation('values', $attr->activeValues()->whereIn('id', $valueIds)->get());
                    return $attr;
                })->filter(function($attr) { return $attr->values->count() > 0; })->toJson() }},
                productPrice: {{ $product->price }},
                productStock: {{ $product->stock_quantity }},
                productSku: '{{ $product->sku }}',
                mainImageUrl: '{{ $product->images->first() ? Storage::url($product->images->first()->image_path) : '' }}',
                currentPrice: {{ $product->getDefaultVariant() ? $product->getDefaultVariant()->price : $product->price }},
                currentStock: {{ $product->getDefaultVariant() ? $product->getDefaultVariant()->stock_quantity : $product->stock_quantity }},
                currentSku: '{{ $product->getDefaultVariant() ? $product->getDefaultVariant()->sku : $product->sku }}',
                currentImageUrl: '{{ $product->getDefaultVariant() && $product->getDefaultVariant()->image_path ? Storage::url($product->getDefaultVariant()->image_path) : ($product->images->first() ? Storage::url($product->images->first()->image_path) : '') }}',

                init() {
                    const defaultVariant = this.variants.find(v => v.is_default);
                    if (defaultVariant) {
                        this.selectedVariant = defaultVariant;
                        Object.entries(defaultVariant.attributes).forEach(([key, value]) => {
                            this.selectedAttributes[key] = value;
                        });
                        this.updateVariant();
                    }
                },

                selectAttributeValue(attributeSlug, value) {
                    this.selectedAttributes[attributeSlug] = value;
                    this.updateVariant();
                },

                updateVariant() {
                    const matchingVariant = this.variants.find(variant => {
                        return Object.keys(this.selectedAttributes).every(key => {
                            return variant.attributes[key] === this.selectedAttributes[key];
                        });
                    });

                    if (matchingVariant) {
                        this.selectedVariant = matchingVariant;
                        this.currentPrice = matchingVariant.price;
                        this.currentStock = matchingVariant.stock_quantity;
                        this.currentSku = matchingVariant.sku;

                        if (matchingVariant.image_path) {
                            this.currentImageUrl = '/storage/' + matchingVariant.image_path;
                            document.getElementById('main-image').src = this.currentImageUrl;
                        } else {
                            this.currentImageUrl = this.mainImageUrl;
                            document.getElementById('main-image').src = this.mainImageUrl;
                        }
                    } else {
                        this.selectedVariant = null;
                        this.currentPrice = this.productPrice;
                        this.currentStock = this.productStock;
                        this.currentSku = this.productSku;
                        this.currentImageUrl = this.mainImageUrl;
                        document.getElementById('main-image').src = this.mainImageUrl;
                    }
                }
            @else
                currentPrice: {{ $product->price }},
                currentStock: {{ $product->stock_quantity }},
                currentSku: '{{ $product->sku }}'
            @endif
        }">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center mb-6">
                <span class="text-4xl font-bold text-indigo-600" x-text="currentPrice.toFixed(2) + ' TND'">
                    {{ number_format($product->getActivePrice(), 2) }} TND
                </span>
            </div>

            @if($product->hasVariants())
                <!-- Variant Selector -->
                <div class="mb-6 space-y-4">
                    <template x-for="attribute in productAttributes" :key="attribute.id">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2" x-text="attribute.name"></label>

                            <!-- Color Swatches -->
                            <template x-if="attribute.display_type === 'color'">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="value in attribute.values" :key="value.id">
                                        <button type="button"
                                                @click="selectAttributeValue(attribute.slug, value.value)"
                                                :class="{
                                                    'ring-2 ring-indigo-600 ring-offset-2': selectedAttributes[attribute.slug] === value.value,
                                                    'hover:scale-110': true
                                                }"
                                                class="w-10 h-10 rounded-full border-2 border-gray-300 transition-all"
                                                :style="'background-color: ' + value.color_code"
                                                :title="value.value">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Button Selection -->
                            <template x-if="attribute.display_type === 'button'">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="value in attribute.values" :key="value.id">
                                        <button type="button"
                                                @click="selectAttributeValue(attribute.slug, value.value)"
                                                :class="{
                                                    'border-indigo-600 bg-indigo-50 text-indigo-700': selectedAttributes[attribute.slug] === value.value,
                                                    'border-gray-300 hover:border-gray-400': selectedAttributes[attribute.slug] !== value.value
                                                }"
                                                class="px-4 py-2 border-2 rounded-md transition-all"
                                                x-text="value.value">
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Select Dropdown -->
                            <template x-if="attribute.display_type === 'select'">
                                <select @change="selectAttributeValue(attribute.slug, $event.target.value)"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Sélectionner --</option>
                                    <template x-for="value in attribute.values" :key="value.id">
                                        <option :value="value.value"
                                                :selected="selectedAttributes[attribute.slug] === value.value"
                                                x-text="value.value">
                                        </option>
                                    </template>
                                </select>
                            </template>
                        </div>
                    </template>
                </div>
            @endif

            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Fournisseur:</span>
                    <span class="font-semibold">{{ $product->supplier->business_name }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Catégorie:</span>
                    <a href="{{ route('products.category', $product->category->slug) }}"
                       class="text-indigo-600 hover:text-indigo-700">
                        {{ $product->category->name }}
                    </a>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">SKU:</span>
                    <span class="font-mono text-sm" x-text="currentSku">{{ $product->sku }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Disponibilité:</span>
                    <span :class="currentStock > 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                        <span x-show="currentStock > 0" x-text="'En stock (' + currentStock + ' unités)'">
                            En stock ({{ $product->getTotalStock() }} unités)
                        </span>
                        <span x-show="currentStock <= 0">
                            Rupture de stock
                        </span>
                    </span>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <div x-show="currentStock > 0">
                <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @if($product->hasVariants())
                        <input type="hidden" name="variant_id" :value="selectedVariant ? selectedVariant.id : ''">
                    @endif

                    <div class="flex items-center space-x-4 mb-4">
                        <label for="quantity" class="text-gray-700 font-medium">Quantité:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" :max="currentStock"
                               class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @auth
                        @if(auth()->user()->isClient())
                            <div class="flex space-x-3">
                                <button type="submit"
                                        class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Ajouter au panier
                                </button>

                                <div x-data="{
                                    inWishlist: {{ auth()->user()->hasInWishlist($product->id) ? 'true' : 'false' }},
                                    toggleWishlist() {
                                        fetch('{{ route('wishlist.toggle', $product) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json'
                                            }
                                        })
                                        .then(r => r.json())
                                        .then(data => {
                                            this.inWishlist = data.action === 'added';
                                            if (document.getElementById('wishlistCount')) {
                                                document.getElementById('wishlistCount').textContent = data.wishlistCount;
                                            }
                                        });
                                    }
                                }">
                                    <button @click="toggleWishlist()"
                                            type="button"
                                            class="bg-white border-2 py-3 px-6 rounded-lg font-semibold hover:bg-gray-50 transition flex items-center justify-center"
                                            :class="inWishlist ? 'border-red-500 text-red-500' : 'border-gray-300 text-gray-700'">
                                        <svg class="w-5 h-5 mr-2 transition"
                                             :class="inWishlist ? 'fill-current' : ''"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span x-text="inWishlist ? 'Dans mes favoris' : 'Ajouter aux favoris'"></span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <p class="text-yellow-700">Seuls les clients peuvent ajouter des produits au panier.</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-gray-600 mb-3">Connectez-vous pour commander ce produit</p>
                            <a href="{{ route('login') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                Se connecter
                            </a>
                        </div>
                    @endauth
                </form>
            </div>

            <div x-show="currentStock <= 0" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <p class="text-red-700">Ce produit est actuellement en rupture de stock</p>
            </div>

            <!-- Product Details -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                @if($product->short_description)
                    <p class="text-gray-700 mb-4">{{ $product->short_description }}</p>
                @endif
                @if($product->description)
                    <div class="text-gray-600 prose max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif

                @if($product->weight || $product->dimensions)
                    <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Spécifications</h3>
                    <ul class="space-y-2">
                        @if($product->weight)
                            <li class="flex items-center text-gray-600">
                                <span class="font-medium mr-2">Poids:</span>
                                {{ $product->weight }} kg
                            </li>
                        @endif
                        @if($product->dimensions)
                            <li class="flex items-center text-gray-600">
                                <span class="font-medium mr-2">Dimensions:</span>
                                {{ $product->dimensions }}
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-16" id="reviews">
        <div class="border-b border-gray-200 pb-4 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Avis clients</h2>
                    <div class="flex items-center mt-2">
                        @if($product->reviewsCount > 0)
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($product->averageRating) ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                         viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-lg font-semibold text-gray-900">{{ number_format($product->averageRating, 1) }}</span>
                                <span class="ml-2 text-gray-600">sur 5 ({{ $product->reviewsCount }} avis)</span>
                            </div>
                        @else
                            <p class="text-gray-600">Aucun avis pour le moment</p>
                        @endif
                    </div>
                </div>
                @if($canReview)
                    <a href="{{ route('reviews.create', $product) }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Laisser un avis
                    </a>
                @endif
            </div>
        </div>

        @if($product->reviewsCount > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Rating Distribution -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Distribution des notes</h3>
                    <div class="space-y-3">
                        @foreach($ratingDistribution as $rating => $data)
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-700 w-12">{{ $rating }} ★</span>
                                <div class="flex-1 mx-3">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 rounded-full h-2"
                                             style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 w-12 text-right">{{ $data['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="lg:col-span-2 space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                                     viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->is_verified_purchase)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Achat vérifié
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $review->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                @auth
                                    @if($review->user_id === auth()->id() && $review->canBeEdited())
                                        <a href="{{ route('reviews.edit', $review) }}"
                                           class="text-sm text-blue-600 hover:text-blue-800">
                                            Modifier
                                        </a>
                                    @endif
                                @endauth
                            </div>

                            @if($review->title)
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                            @endif
                            <p class="text-gray-700 mb-4">{{ $review->comment }}</p>

                            @auth
                                <form action="{{ route('reviews.helpful', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                        </svg>
                                        Utile ({{ $review->helpful_count }})
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-gray-600">
                                    {{ $review->helpful_count }} {{ Str::plural('personne', $review->helpful_count) }} trouvent cet avis utile
                                </span>
                            @endauth
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->fragment('reviews')->links() }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun avis pour le moment</h3>
                <p class="mt-1 text-sm text-gray-500">Soyez le premier à laisser un avis sur ce produit</p>
                @if($canReview)
                    <div class="mt-6">
                        <a href="{{ route('reviews.create', $product) }}"
                           class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition">
                            Laisser le premier avis
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produits similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}">
                            <div class="h-48 bg-gray-200">
                                @if($relatedProduct->images->first())
                                    <img src="{{ Storage::url($relatedProduct->images->first()->image_path) }}"
                                         alt="{{ $relatedProduct->name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}">
                                <h3 class="font-semibold text-gray-900 hover:text-indigo-600 line-clamp-2">
                                    {{ $relatedProduct->name }}
                                </h3>
                            </a>
                            <p class="text-xl font-bold text-indigo-600 mt-2">
                                {{ number_format($relatedProduct->price, 2) }} TND
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recently Viewed Products -->
    <div class="mt-16">
        <x-recently-viewed-products />
    </div>
</div>

<script>
function changeImage(url) {
    document.getElementById('main-image').src = url;
}
</script>
@endsection
