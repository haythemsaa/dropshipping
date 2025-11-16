@extends('layouts.frontend')

@section('title', 'Compte en attente - Dropshipping TN')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Compte en attente d'approbation
            </h2>

            <p class="text-gray-600 mb-6">
                Votre demande de compte fournisseur a bien été reçue et est en cours de vérification par notre équipe.
            </p>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Le processus d'approbation prend généralement entre 24 et 48 heures. Vous recevrez une notification par email une fois votre compte approuvé.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3 text-left mb-6">
                <p class="text-sm text-gray-600">
                    <strong>Informations de votre compte :</strong>
                </p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Nom :</span>
                        <span class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email :</span>
                        <span class="text-sm font-medium text-gray-900">{{ Auth::user()->email }}</span>
                    </div>
                    @if(Auth::user()->business_name)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Entreprise :</span>
                            <span class="text-sm font-medium text-gray-900">{{ Auth::user()->business_name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Statut :</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            En attente
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-3">
                <a href="{{ route('home') }}"
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                    Retour à l'accueil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Déconnexion
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Des questions ? Contactez-nous à
                    <a href="mailto:support@dropshipping.tn" class="text-indigo-600 hover:text-indigo-700">
                        support@dropshipping.tn
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
