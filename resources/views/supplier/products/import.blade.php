@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Import de produits en masse</h1>
        <p class="mt-1 text-sm text-gray-600">
            Importez plusieurs produits à la fois en utilisant un fichier CSV ou Excel
        </p>
    </div>

    <!-- Messages de statut -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Erreurs d'import -->
    @if(session('import_errors'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-red-800 mb-2">Erreurs d'import :</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach(session('import_errors') as $error)
                    <div class="text-sm text-red-700">
                        <span class="font-semibold">Ligne {{ $error['row'] }}:</span>
                        <ul class="ml-4 list-disc">
                            @foreach($error['errors'] as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Instructions -->
        <div class="lg:col-span-2">
            <!-- Étape 1: Télécharger le template -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-bold">
                            1
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Téléchargez le template
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Utilisez notre template pré-formaté pour importer vos produits. Il contient des exemples et les colonnes requises.
                        </p>
                        <a href="{{ route('supplier.products.template') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger le template Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Étape 2: Remplir le fichier -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-bold">
                            2
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Remplissez vos informations
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Ouvrez le template avec Excel, LibreOffice ou Google Sheets et remplissez-le avec vos produits.
                        </p>
                        <div class="bg-gray-50 rounded-md p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Colonnes requises :</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><span class="font-semibold text-red-600">nom*</span> - Nom du produit (obligatoire)</li>
                                <li><span class="font-semibold text-red-600">prix*</span> - Prix en dinars (obligatoire)</li>
                                <li><span class="font-semibold">description</span> - Description détaillée</li>
                                <li><span class="font-semibold">categorie</span> - Nom de la catégorie</li>
                                <li><span class="font-semibold">stock</span> - Quantité en stock</li>
                                <li><span class="font-semibold">sku</span> - Code produit (généré automatiquement si vide)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Étape 3: Importer le fichier -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-bold">
                            3
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Importez votre fichier
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Sélectionnez votre fichier rempli et lancez l'import. Les produits seront créés et mis en attente d'approbation.
                        </p>

                        <form action="{{ route('supplier.products.import.process') }}" method="POST" enctype="multipart/form-data" id="import-form">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fichier à importer
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Cliquez pour parcourir</span> ou glissez-déposez
                                            </p>
                                            <p class="text-xs text-gray-500">CSV, XLSX ou XLS (Max 10 MB)</p>
                                        </div>
                                        <input id="file-upload" name="file" type="file" class="hidden" accept=".csv,.xlsx,.xls" required onchange="updateFileName(this)">
                                    </label>
                                </div>
                                <p id="file-name" class="mt-2 text-sm text-gray-500"></p>
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ route('supplier.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    ← Retour à la liste
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Lancer l'import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="lg:col-span-1">
            <!-- Conseils -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h3 class="text-sm font-medium text-blue-900 mb-3 flex items-center">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Conseils
                </h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Ne modifiez pas les en-têtes de colonnes</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Utilisez des prix sans symbole (ex: 125.00)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Les catégories doivent correspondre aux catégories existantes</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Évitez les lignes vides</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Maximum 1000 produits par import</span>
                    </li>
                </ul>
            </div>

            <!-- Catégories disponibles -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Catégories disponibles</h3>
                <div class="space-y-1 text-sm text-gray-600 max-h-64 overflow-y-auto">
                    @foreach($categories->where('parent_id', null) as $category)
                        <div class="mb-2">
                            <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                            @foreach($categories->where('parent_id', $category->id) as $subcategory)
                                <p class="ml-4 text-gray-600">→ {{ $subcategory->name }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = 'Fichier sélectionné: ' + fileName;
        fileNameDisplay.classList.add('text-green-600', 'font-medium');
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endpush
@endsection
