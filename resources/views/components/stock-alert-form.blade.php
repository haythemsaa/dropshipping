@props(['productId', 'variantId' => null])

<div x-data="{
    email: '{{ auth()->check() ? auth()->user()->email : '' }}',
    loading: false,
    message: '',
    messageType: '',
    subscribed: false,

    subscribe() {
        this.loading = true;
        this.message = '';

        fetch('{{ route('stock-alerts.subscribe') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: {{ $productId }},
                variant_id: {{ $variantId ?? 'null' }},
                email: this.email
            })
        })
        .then(r => r.json())
        .then(data => {
            this.loading = false;
            this.message = data.message;
            this.messageType = data.success ? 'success' : 'error';

            if (data.success) {
                this.subscribed = true;
                setTimeout(() => { this.message = ''; }, 5000);
            }
        })
        .catch(error => {
            this.loading = false;
            this.message = 'Une erreur est survenue. Veuillez réessayer.';
            this.messageType = 'error';
        });
    }
}" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
    <div class="flex items-start space-x-3 mb-4">
        <div class="flex-shrink-0">
            <i class="fas fa-bell text-2xl text-yellow-600"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Produit en rupture de stock</h3>
            <p class="text-sm text-gray-600">
                Saisissez votre email pour être notifié dès que ce produit sera de nouveau disponible.
            </p>
        </div>
    </div>

    <form @submit.prevent="subscribe()" x-show="!subscribed">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="email"
                   x-model="email"
                   required
                   placeholder="Votre adresse email"
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">

            <button type="submit"
                    :disabled="loading"
                    class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-yellow-700 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap flex items-center justify-center">
                <i class="fas fa-bell mr-2"></i>
                <span x-show="!loading">Me notifier</span>
                <span x-show="loading">
                    <i class="fas fa-spinner fa-spin"></i> Inscription...
                </span>
            </button>
        </div>

        <!-- Message -->
        <div x-show="message"
             x-transition
             :class="{
                 'text-green-800 bg-green-100 border-green-200': messageType === 'success',
                 'text-red-800 bg-red-100 border-red-200': messageType === 'error'
             }"
             class="mt-4 p-3 rounded-lg border text-sm font-medium">
            <span x-text="message"></span>
        </div>
    </form>

    <div x-show="subscribed" class="flex items-center text-green-700">
        <i class="fas fa-check-circle text-xl mr-2"></i>
        <span class="font-medium">Vous serez notifié par email dès que ce produit sera disponible!</span>
    </div>
</div>
