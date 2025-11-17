@props(['layout' => 'horizontal'])

@if($layout === 'horizontal')
    <div {{ $attributes->merge(['class' => 'bg-gray-50 rounded-lg p-6']) }}>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Secure Payment -->
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-lock text-2xl text-green-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">Paiement Sécurisé</h4>
                <p class="text-xs text-gray-600">Cryptage SSL</p>
            </div>

            <!-- Money Back -->
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-shield-alt text-2xl text-blue-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">Garantie Satisfait</h4>
                <p class="text-xs text-gray-600">14 jours retour</p>
            </div>

            <!-- Fast Shipping -->
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-shipping-fast text-2xl text-purple-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">Livraison Rapide</h4>
                <p class="text-xs text-gray-600">1-5 jours</p>
            </div>

            <!-- Support -->
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-headset text-2xl text-orange-600"></i>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1">Support 24/7</h4>
                <p class="text-xs text-gray-600">Assistance rapide</p>
            </div>
        </div>
    </div>
@else
    <!-- Vertical/Compact Layout -->
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        <div class="flex items-center space-x-3 text-sm">
            <i class="fas fa-lock text-green-600"></i>
            <span class="text-gray-700"><span class="font-semibold">Paiement 100% sécurisé</span> - Cryptage SSL</span>
        </div>
        <div class="flex items-center space-x-3 text-sm">
            <i class="fas fa-shield-alt text-blue-600"></i>
            <span class="text-gray-700"><span class="font-semibold">Garantie satisfait</span> - Retour sous 14 jours</span>
        </div>
        <div class="flex items-center space-x-3 text-sm">
            <i class="fas fa-shipping-fast text-purple-600"></i>
            <span class="text-gray-700"><span class="font-semibold">Livraison rapide</span> - Partout en Tunisie</span>
        </div>
        <div class="flex items-center space-x-3 text-sm">
            <i class="fas fa-headset text-orange-600"></i>
            <span class="text-gray-700"><span class="font-semibold">Support client</span> - Disponible 24/7</span>
        </div>
    </div>
@endif
