@extends('layouts.dashboard')

@section('title', 'Modifier la catégorie - Admin')

@section('sidebar')
<aside class="w-64 bg-white shadow-md">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900">Administration</h2>
        <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
    </div>
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Fournisseurs
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Catégories
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Produits
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Commandes
        </a>
        <a href="{{ route('admin.commissions.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Commissions
        </a>
    </nav>
</aside>
@endsection

@section('content')
<div class="p-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier la catégorie</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $category->name }}</p>
            </div>
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $category->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Catégorie parente
                            </label>
                            <select name="parent_id" id="parent_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Catégorie principale --</option>
                                @foreach($parentCategories as $parent)
                                    @if($parent->id !== $category->id)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @if($category->children->count() > 0)
                                <p class="mt-1 text-sm text-gray-500">
                                    Cette catégorie a {{ $category->children->count() }} sous-catégorie(s)
                                </p>
                            @endif
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                                    Ordre d'affichage
                                </label>
                                <input type="number" name="order" id="order" min="0"
                                       value="{{ old('order', $category->order) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Plus le nombre est bas, plus la catégorie apparaît en premier</p>
                            </div>

                            <div class="flex items-center pt-6">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Catégorie active
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Enregistrer les modifications
                        </button>

                        @if($category->products_count == 0)
                            <button type="button" onclick="document.getElementById('delete-form').submit()"
                                    class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition">
                                Supprimer la catégorie
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Category Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Statut</p>
                        <span class="inline-flex items-center mt-1 px-2 py-1 text-xs rounded-full
                            @if($category->is_active) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-gray-600">Type</p>
                        <p class="text-gray-900 mt-1 font-medium">
                            @if($category->parent_id)
                                Sous-catégorie
                            @else
                                Catégorie principale
                            @endif
                        </p>
                    </div>

                    @if($category->parent)
                        <div>
                            <p class="text-gray-600">Catégorie parente</p>
                            <p class="text-gray-900 mt-1 font-medium">{{ $category->parent->name }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-gray-600">Produits</p>
                        <p class="text-gray-900 mt-1 font-medium">{{ $category->products_count }} produit(s)</p>
                    </div>

                    <div>
                        <p class="text-gray-600">Sous-catégories</p>
                        <p class="text-gray-900 mt-1 font-medium">{{ $category->children->count() }} sous-catégorie(s)</p>
                    </div>

                    <div>
                        <p class="text-gray-600">Créée le</p>
                        <p class="text-gray-900 mt-1">{{ $category->created_at->format('d/m/Y') }}</p>
                    </div>

                    @if($category->updated_at != $category->created_at)
                        <div>
                            <p class="text-gray-600">Modifiée le</p>
                            <p class="text-gray-900 mt-1">{{ $category->updated_at->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sub-categories List -->
            @if($category->children->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sous-catégories</h3>

                    <div class="space-y-2">
                        @foreach($category->children as $child)
                            <a href="{{ route('admin.categories.edit', $child) }}"
                               class="block p-3 rounded-md border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900">{{ $child->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $child->products_count }} produits</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Warning if has products -->
            @if($category->products_count > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Cette catégorie contient des produits et ne peut pas être supprimée.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
@if($category->products_count == 0)
    <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

<script>
    document.getElementById('delete-form')?.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
            e.preventDefault();
        }
    });
</script>
@endsection
