<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- En-tête -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Modifier votre avis</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Vous pouvez modifier votre avis dans les 48h après sa création
                        </p>
                    </div>

                    <!-- Avertissement temps restant -->
                    @php
                        $hoursLeft = 48 - $review->created_at->diffInHours(now());
                    @endphp
                    @if($hoursLeft < 24)
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-800 font-medium">
                                        Il vous reste environ {{ $hoursLeft }} heure{{ $hoursLeft > 1 ? 's' : '' }} pour modifier cet avis
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Produit -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start space-x-4">
                            @if($product->primaryImage)
                                <img src="{{ Storage::url($product->primaryImage->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-20 h-20 object-cover rounded">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $product->category->name }}</p>
                                <p class="text-xs text-gray-500 mt-2">
                                    Avis créé le {{ $review->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire -->
                    <form action="{{ route('reviews.update', $review) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Note -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Note <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1" x-data="{ rating: {{ old('rating', $review->rating) }} }">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                @click="rating = {{ $i }}"
                                                class="focus:outline-none transition-colors">
                                            <svg class="w-10 h-10"
                                                 :class="rating >= {{ $i }} ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        </button>
                                        <input type="radio" name="rating" value="{{ $i }}"
                                               :checked="rating === {{ $i }}" class="hidden">
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 ml-2" x-data="{ rating: {{ old('rating', $review->rating) }} }">
                                    <span x-text="rating + '/5'"></span>
                                </span>
                            </div>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Titre -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Titre de votre avis (optionnel)
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title', $review->title) }}"
                                   maxlength="200"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commentaire -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700">
                                Votre avis <span class="text-red-500">*</span>
                            </label>
                            <textarea name="comment"
                                      id="comment"
                                      rows="6"
                                      maxlength="2000"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('comment', $review->comment) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Minimum 10 caractères, maximum 2000</p>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-gray-600 hover:text-gray-900">
                                    Annuler
                                </a>
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        Supprimer l'avis
                                    </button>
                                </form>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
