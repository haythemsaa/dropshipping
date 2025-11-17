<x-dashboard-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Modération des avis</h1>
                <p class="mt-1 text-sm text-gray-600">Gérez les avis clients sur les produits</p>
            </div>

            <!-- Onglets de statut -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
                       class="@if(request('status', 'pending') === 'pending') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        En attente
                        @if($statusCounts['pending'] > 0)
                            <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs font-medium">
                                {{ $statusCounts['pending'] }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
                       class="@if(request('status') === 'approved') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Approuvés
                        <span class="ml-2 text-gray-400 text-xs">{{ $statusCounts['approved'] }}</span>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}"
                       class="@if(!request()->has('status')) border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Tous
                        <span class="ml-2 text-gray-400 text-xs">{{ $statusCounts['all'] }}</span>
                    </a>
                </nav>
            </div>

            <!-- Filtres -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border p-4">
                <form method="GET" class="flex items-end gap-4">
                    <input type="hidden" name="status" value="{{ request('status', 'pending') }}">

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Filtrer par note
                        </label>
                        <select name="rating"
                                onchange="this.form.submit()"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">Toutes les notes</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    @if(request()->hasAny(['rating']))
                        <a href="{{ route('admin.reviews.index', ['status' => request('status', 'pending')]) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                            Réinitialiser
                        </a>
                    @endif
                </form>
            </div>

            <!-- Actions en masse -->
            <form id="bulkForm" method="POST" class="mb-4">
                @csrf
                <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border p-4">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="selectAll" class="text-sm font-medium text-gray-700">
                            Tout sélectionner
                        </label>
                        <span id="selectedCount" class="text-sm text-gray-500">0 sélectionné(s)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button"
                                onclick="bulkAction('{{ route('admin.reviews.bulk-approve') }}')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50"
                                id="bulkApproveBtn" disabled>
                            Approuver la sélection
                        </button>
                        <button type="button"
                                onclick="if(confirm('Supprimer les avis sélectionnés ?')) bulkAction('{{ route('admin.reviews.bulk-delete') }}')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                                id="bulkDeleteBtn" disabled>
                            Supprimer la sélection
                        </button>
                    </div>
                </div>
            </form>

            <!-- Liste des avis -->
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                @if($reviews->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($reviews as $review)
                            <div class="p-6 hover:bg-gray-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <!-- Checkbox -->
                                        <input type="checkbox"
                                               name="review_ids[]"
                                               value="{{ $review->id }}"
                                               class="review-checkbox mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                        <!-- Image produit -->
                                        @if($review->product->primaryImage)
                                            <img src="{{ Storage::url($review->product->primaryImage->image_path) }}"
                                                 alt="{{ $review->product->name }}"
                                                 class="w-20 h-20 object-cover rounded flex-shrink-0">
                                        @else
                                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Contenu de l'avis -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <a href="{{ route('products.show', $review->product) }}"
                                                       target="_blank"
                                                       class="font-semibold text-gray-900 hover:text-blue-600">
                                                        {{ $review->product->name }}
                                                    </a>
                                                    <div class="flex items-center mt-1 space-x-3">
                                                        <!-- Étoiles -->
                                                        <div class="flex items-center">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                                                     viewBox="0 0 24 24">
                                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                                </svg>
                                                            @endfor
                                                            <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                                        </div>

                                                        <!-- Badge achat vérifié -->
                                                        @if($review->is_verified_purchase)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Achat vérifié
                                                            </span>
                                                        @endif

                                                        <!-- Statut -->
                                                        @if($review->is_approved)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Approuvé
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                En attente
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Titre de l'avis -->
                                            @if($review->title)
                                                <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                                            @endif

                                            <!-- Commentaire -->
                                            <p class="text-gray-700 text-sm mb-3">{{ $review->comment }}</p>

                                            <!-- Info auteur et date -->
                                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                                <span class="font-medium">{{ $review->user->name }}</span>
                                                <span>•</span>
                                                <span>{{ $review->created_at->format('d/m/Y à H:i') }}</span>
                                                @if($review->helpful_count > 0)
                                                    <span>•</span>
                                                    <span>{{ $review->helpful_count }} {{ Str::plural('personne', $review->helpful_count) }} trouvent cet avis utile</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col space-y-2 ml-4">
                                        @if(!$review->is_approved)
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 whitespace-nowrap">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Approuver
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded hover:bg-yellow-700 whitespace-nowrap">
                                                    Retirer
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                              onsubmit="return confirm('Supprimer cet avis définitivement ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($reviews->hasPages())
                        <div class="px-6 py-4 border-t">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun avis</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request('status') === 'pending')
                                Aucun avis en attente de modération
                            @elseif(request('status') === 'approved')
                                Aucun avis approuvé
                            @else
                                Aucun avis trouvé
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Gestion de la sélection
        const selectAllCheckbox = document.getElementById('selectAll');
        const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const bulkApproveBtn = document.getElementById('bulkApproveBtn');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function updateSelectedCount() {
            const count = document.querySelectorAll('.review-checkbox:checked').length;
            selectedCount.textContent = count + ' sélectionné(s)';
            bulkApproveBtn.disabled = count === 0;
            bulkDeleteBtn.disabled = count === 0;
        }

        selectAllCheckbox.addEventListener('change', function() {
            reviewCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        reviewCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        function bulkAction(url) {
            const form = document.getElementById('bulkForm');
            form.action = url;
            form.submit();
        }
    </script>
    @endpush
</x-dashboard-layout>
