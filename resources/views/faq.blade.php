@extends('layouts.frontend')

@section('title', 'Questions Fréquentes (FAQ) - Dropshipping TN')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <i class="fas fa-question-circle text-purple-600 mr-2"></i>
            Questions Fréquentes (FAQ)
        </h1>
        <p class="text-lg text-gray-600">
            Trouvez rapidement des réponses à vos questions les plus courantes
        </p>
    </div>

    <!-- FAQ Accordion -->
    <div class="space-y-4" x-data="{ activeQuestion: null }">

        <!-- Question 1 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 1 ? null : 1"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-truck text-purple-600 mr-2"></i>
                    Quels sont les délais de livraison ?
                </span>
                <i class="fas" :class="activeQuestion === 1 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 1"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Les délais de livraison varient selon votre localisation :
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Grand Tunis : 1-2 jours ouvrables</li>
                    <li>Villes principales : 2-4 jours ouvrables</li>
                    <li>Autres régions : 3-5 jours ouvrables</li>
                </ul>
            </div>
        </div>

        <!-- Question 2 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 2 ? null : 2"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                    Quels modes de paiement acceptez-vous ?
                </span>
                <i class="fas" :class="activeQuestion === 2 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 2"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Nous acceptons plusieurs modes de paiement :
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Paiement à la livraison (Cash On Delivery)</li>
                    <li>e-Dinar</li>
                    <li>Cartes bancaires (Visa, Mastercard)</li>
                </ul>
            </div>
        </div>

        <!-- Question 3 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 3 ? null : 3"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-undo text-purple-600 mr-2"></i>
                    Puis-je retourner un produit ?
                </span>
                <i class="fas" :class="activeQuestion === 3 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 3"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Oui, vous pouvez retourner un produit dans les 14 jours suivant la réception pour un remboursement complet. Le produit doit être dans son emballage d'origine, non utilisé et avec tous les accessoires. Les frais de retour sont à votre charge sauf en cas de produit défectueux.
            </div>
        </div>

        <!-- Question 4 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 4 ? null : 4"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                    Mes informations personnelles sont-elles sécurisées ?
                </span>
                <i class="fas" :class="activeQuestion === 4 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Oui, absolument. Nous utilisons le cryptage SSL pour protéger toutes vos transactions et informations personnelles. Nous ne partageons jamais vos données avec des tiers sans votre consentement explicite.
            </div>
        </div>

        <!-- Question 5 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 5 ? null : 5"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-box text-purple-600 mr-2"></i>
                    Comment suivre ma commande ?
                </span>
                <i class="fas" :class="activeQuestion === 5 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Après votre commande, vous recevrez un email de confirmation avec un numéro de suivi. Vous pouvez suivre l'état de votre commande en vous connectant à votre compte et en consultant la section "Mes commandes".
            </div>
        </div>

        <!-- Question 6 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 6 ? null : 6"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-store text-purple-600 mr-2"></i>
                    Comment devenir fournisseur sur la plateforme ?
                </span>
                <i class="fas" :class="activeQuestion === 6 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 6"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Pour devenir fournisseur, créez un compte en sélectionnant "Fournisseur" lors de l'inscription. Remplissez votre profil professionnel avec les informations de votre entreprise. Notre équipe examinera votre demande sous 48h et vous contactera pour valider votre compte.
            </div>
        </div>

        <!-- Question 7 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 7 ? null : 7"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-percentage text-purple-600 mr-2"></i>
                    Y a-t-il des frais cachés ?
                </span>
                <i class="fas" :class="activeQuestion === 7 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 7"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Non, tous les prix affichés incluent toutes les taxes. Les seuls frais supplémentaires sont les frais de livraison, qui sont clairement indiqués lors du passage de commande avant validation.
            </div>
        </div>

        <!-- Question 8 -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <button @click="activeQuestion = activeQuestion === 8 ? null : 8"
                    class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                <span class="font-semibold text-gray-900">
                    <i class="fas fa-headset text-purple-600 mr-2"></i>
                    Comment contacter le service client ?
                </span>
                <i class="fas" :class="activeQuestion === 8 ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400"></i>
            </button>
            <div x-show="activeQuestion === 8"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="px-6 pb-4 text-gray-600">
                Notre service client est disponible du lundi au vendredi de 9h à 18h :
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Email : support@dropshipping-tn.com</li>
                    <li>Téléphone : +216 XX XXX XXX</li>
                    <li>Formulaire de contact sur le site</li>
                </ul>
            </div>
        </div>

    </div>

    <!-- Contact CTA -->
    <div class="mt-12 text-center bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Vous n'avez pas trouvé votre réponse ?</h3>
        <p class="text-gray-600 mb-4">Notre équipe est là pour vous aider</p>
        <a href="mailto:support@dropshipping-tn.com"
           class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
            <i class="fas fa-envelope mr-2"></i>
            Contactez-nous
        </a>
    </div>
</div>
@endsection
