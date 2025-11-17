<div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg p-8 text-white" x-data="{
    email: '',
    name: '',
    loading: false,
    message: '',
    messageType: '',

    subscribe() {
        this.loading = true;
        this.message = '';

        fetch('{{ route('newsletter.subscribe') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: this.email,
                name: this.name
            })
        })
        .then(r => r.json())
        .then(data => {
            this.loading = false;
            this.message = data.message;
            this.messageType = data.success ? 'success' : 'error';

            if (data.success) {
                this.email = '';
                this.name = '';
                setTimeout(() => { this.message = ''; }, 5000);
            }
        })
        .catch(error => {
            this.loading = false;
            this.message = 'Une erreur est survenue. Veuillez réessayer.';
            this.messageType = 'error';
        });
    }
}">
    <div class="max-w-4xl mx-auto text-center">
        <i class="fas fa-envelope text-4xl mb-4"></i>
        <h3 class="text-2xl font-bold mb-2">Restez informé de nos nouveautés</h3>
        <p class="mb-6 text-purple-100">Inscrivez-vous à notre newsletter et recevez nos dernières offres et nouveaux produits</p>

        <form @submit.prevent="subscribe()" class="max-w-xl mx-auto">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="email"
                       x-model="email"
                       required
                       placeholder="Votre adresse email"
                       class="flex-1 px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-300">

                <button type="submit"
                        :disabled="loading"
                        class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                    <span x-show="!loading">S'inscrire</span>
                    <span x-show="loading">
                        <i class="fas fa-spinner fa-spin"></i> Inscription...
                    </span>
                </button>
            </div>

            <!-- Message -->
            <div x-show="message"
                 x-transition
                 :class="{
                     'text-green-200 bg-green-600': messageType === 'success',
                     'text-red-200 bg-red-600': messageType === 'error'
                 }"
                 class="mt-4 p-3 rounded-lg text-sm font-medium">
                <span x-text="message"></span>
            </div>
        </form>

        <p class="mt-4 text-xs text-purple-200">
            En vous inscrivant, vous acceptez de recevoir nos emails marketing. Vous pouvez vous désinscrire à tout moment.
        </p>
    </div>
</div>
