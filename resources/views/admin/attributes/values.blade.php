@extends('layouts.dashboard')

@section('title', 'Valeurs de l\'attribut : ' . $attribute->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.attributes.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $attribute->name }}</h1>
                    @if($attribute->display_type === 'color')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Couleur
                        </span>
                    @elseif($attribute->display_type === 'button')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Bouton
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Select
                        </span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Gérez les valeurs possibles pour cet attribut
                </p>
            </div>
            <button @click="showCreateModal = true"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle Valeur
            </button>
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

    <!-- Liste des valeurs -->
    <div class="bg-white rounded-lg shadow overflow-hidden" x-data="{
        showCreateModal: false,
        showEditModal: false,
        editValue: null,
        isColorAttribute: {{ $attribute->display_type === 'color' ? 'true' : 'false' }}
    }">
        @if($values->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($values as $value)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1">
                                @if($value->hasColor())
                                    <div class="w-10 h-10 rounded-full border-2 border-gray-300 flex-shrink-0"
                                         style="background-color: {{ $value->color_code }};"
                                         title="{{ $value->color_code }}">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                        {{ $value->value }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $value->slug }}
                                    </p>
                                    @if($value->hasColor())
                                        <p class="text-xs text-gray-400 mt-1 font-mono">
                                            {{ $value->color_code }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('admin.attributes.values.toggle-status', [$attribute, $value]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex-shrink-0">
                                    @if($value->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactif
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">Position: {{ $value->position }}</span>
                            <div class="flex items-center space-x-2">
                                <button @click="editValue = {{ $value->toJson() }}; showEditModal = true"
                                        class="text-blue-600 hover:text-blue-900 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette valeur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $values->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune valeur</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle valeur pour cet attribut.</p>
                <div class="mt-6">
                    <button @click="showCreateModal = true"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer une valeur
                    </button>
                </div>
            </div>
        @endif

        <!-- Modale Création -->
        <div x-show="showCreateModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showCreateModal = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter une valeur</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="value" class="block text-sm font-medium text-gray-700">Valeur *</label>
                                    <input type="text" name="value" id="value" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ex: Rouge, M, Coton">
                                </div>

                                <div x-show="isColorAttribute">
                                    <label for="color_code" class="block text-sm font-medium text-gray-700">Code couleur</label>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <input type="color" name="color_code" id="color_code"
                                               class="h-10 w-16 border-gray-300 rounded-md cursor-pointer">
                                        <input type="text" name="color_code_text" id="color_code_text"
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="#FF0000"
                                               pattern="^#[0-9A-Fa-f]{6}$">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Format hexadécimal requis (ex: #FF0000)</p>
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                                    <input type="number" name="position" id="position" value="0" min="0"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Créer
                            </button>
                            <button type="button" @click="showCreateModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modale Édition -->
        <div x-show="showEditModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showEditModal = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form :action="`/admin/attributs/{{ $attribute->id }}/valeurs/${editValue?.id}`" method="POST" x-show="editValue">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier la valeur</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="edit_value" class="block text-sm font-medium text-gray-700">Valeur *</label>
                                    <input type="text" name="value" id="edit_value" required
                                           :value="editValue?.value"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div x-show="isColorAttribute">
                                    <label for="edit_color_code" class="block text-sm font-medium text-gray-700">Code couleur</label>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <input type="color" name="color_code" id="edit_color_code"
                                               :value="editValue?.color_code || '#000000'"
                                               class="h-10 w-16 border-gray-300 rounded-md cursor-pointer">
                                        <input type="text" name="color_code_text" id="edit_color_code_text"
                                               :value="editValue?.color_code"
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                               pattern="^#[0-9A-Fa-f]{6}$">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_position" class="block text-sm font-medium text-gray-700">Position</label>
                                    <input type="number" name="position" id="edit_position" min="0"
                                           :value="editValue?.position"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                                           :checked="editValue?.is_active"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Mettre à jour
                            </button>
                            <button type="button" @click="showEditModal = false; editValue = null"
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

@push('scripts')
<script>
    // Sync color picker with text input
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('color_code');
        const colorText = document.getElementById('color_code_text');

        if (colorPicker && colorText) {
            colorPicker.addEventListener('input', function() {
                colorText.value = this.value.toUpperCase();
            });

            colorText.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    colorPicker.value = this.value;
                }
            });
        }

        // Same for edit modal
        const editColorPicker = document.getElementById('edit_color_code');
        const editColorText = document.getElementById('edit_color_code_text');

        if (editColorPicker && editColorText) {
            editColorPicker.addEventListener('input', function() {
                editColorText.value = this.value.toUpperCase();
            });

            editColorText.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    editColorPicker.value = this.value;
                }
            });
        }
    });
</script>
@endpush
@endsection
