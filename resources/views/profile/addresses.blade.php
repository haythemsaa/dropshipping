@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Mes adresses</h1>
        <p class="text-sm text-gray-600 mt-1">Gérez vos adresses de livraison et de facturation</p>
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Add New Address Card -->
        <div class="bg-white rounded-lg shadow-md border-2 border-dashed border-gray-300 hover:border-indigo-500 transition">
            <button type="button"
                    onclick="document.getElementById('create-address-modal').classList.remove('hidden')"
                    class="w-full h-full p-8 flex flex-col items-center justify-center text-gray-400 hover:text-indigo-600 transition">
                <svg class="h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-sm font-medium">Ajouter une adresse</span>
            </button>
        </div>

        <!-- Address Cards -->
        @foreach($addresses as $address)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $address->label }}</h3>
                            @if($address->is_default)
                                <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    Par défaut
                                </span>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $address->type === 'shipping' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $address->type === 'shipping' ? 'Livraison' : 'Facturation' }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 space-y-1 mb-4">
                        <p class="font-medium text-gray-900">{{ $address->full_name }}</p>
                        <p>{{ $address->address_line1 }}</p>
                        @if($address->address_line2)
                            <p>{{ $address->address_line2 }}</p>
                        @endif
                        <p>{{ $address->postal_code }} {{ $address->city }}</p>
                        <p>{{ $address->state }}</p>
                        <p class="font-medium">Tunisie</p>
                        <p class="mt-2">
                            <svg class="inline h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ $address->phone }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2 pt-4 border-t border-gray-200">
                        @if(!$address->is_default)
                            <form action="{{ route('profile.addresses.setDefault', $address) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="w-full text-sm px-3 py-2 text-indigo-600 border border-indigo-600 rounded-md hover:bg-indigo-50 transition">
                                    Définir par défaut
                                </button>
                            </form>
                        @endif

                        <button type="button"
                                onclick="openEditModal({{ json_encode($address) }})"
                                class="flex-1 text-sm px-3 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            Modifier
                        </button>

                        @if(!$address->is_default)
                            <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette adresse ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-sm px-3 py-2 text-red-600 border border-red-300 rounded-md hover:bg-red-50 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($addresses->count() == 0)
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Vous n'avez pas encore d'adresse enregistrée. Ajoutez une adresse pour faciliter vos commandes futures.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Create Address Modal -->
<div id="create-address-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Ajouter une adresse</h3>
            <button type="button"
                    onclick="document.getElementById('create-address-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.addresses.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de l'adresse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="label" id="label" required
                           placeholder="Ex: Maison, Bureau..."
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="shipping">Livraison</option>
                        <option value="billing">Facturation</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" id="full_name" required
                       value="{{ Auth::user()->name }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Téléphone <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" id="phone" required
                       placeholder="+216 XX XXX XXX"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse ligne 1 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address_line1" id="address_line1" required
                       placeholder="Numéro et nom de rue"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse ligne 2
                </label>
                <input type="text" name="address_line2" id="address_line2"
                       placeholder="Appartement, bâtiment, étage (optionnel)"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="city" id="city" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Code postal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="postal_code" id="postal_code" required
                           placeholder="Ex: 1000"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                    Gouvernorat <span class="text-red-500">*</span>
                </label>
                <select name="state" id="state" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sélectionnez un gouvernorat</option>
                    <option value="Tunis">Tunis</option>
                    <option value="Ariana">Ariana</option>
                    <option value="Ben Arous">Ben Arous</option>
                    <option value="Manouba">Manouba</option>
                    <option value="Nabeul">Nabeul</option>
                    <option value="Zaghouan">Zaghouan</option>
                    <option value="Bizerte">Bizerte</option>
                    <option value="Béja">Béja</option>
                    <option value="Jendouba">Jendouba</option>
                    <option value="Kef">Kef</option>
                    <option value="Siliana">Siliana</option>
                    <option value="Sousse">Sousse</option>
                    <option value="Monastir">Monastir</option>
                    <option value="Mahdia">Mahdia</option>
                    <option value="Sfax">Sfax</option>
                    <option value="Kairouan">Kairouan</option>
                    <option value="Kasserine">Kasserine</option>
                    <option value="Sidi Bouzid">Sidi Bouzid</option>
                    <option value="Gabès">Gabès</option>
                    <option value="Médenine">Médenine</option>
                    <option value="Tataouine">Tataouine</option>
                    <option value="Gafsa">Gafsa</option>
                    <option value="Tozeur">Tozeur</option>
                    <option value="Kébili">Kébili</option>
                </select>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_default" id="is_default" value="1"
                       {{ $addresses->count() == 0 ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_default" class="ml-2 block text-sm text-gray-900">
                    Définir comme adresse par défaut
                </label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button"
                        onclick="document.getElementById('create-address-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Ajouter l'adresse
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Address Modal -->
<div id="edit-address-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Modifier l'adresse</h3>
            <button type="button"
                    onclick="document.getElementById('edit-address-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="edit-address-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <!-- Same form fields as create, will be populated by JavaScript -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_label" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de l'adresse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="label" id="edit_label" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="edit_type" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="shipping">Livraison</option>
                        <option value="billing">Facturation</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="edit_full_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" id="edit_full_name" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Téléphone <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" id="edit_phone" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="edit_address_line1" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse ligne 1 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address_line1" id="edit_address_line1" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="edit_address_line2" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse ligne 2
                </label>
                <input type="text" name="address_line2" id="edit_address_line2"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_city" class="block text-sm font-medium text-gray-700 mb-1">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="city" id="edit_city" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="edit_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Code postal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="postal_code" id="edit_postal_code" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label for="edit_state" class="block text-sm font-medium text-gray-700 mb-1">
                    Gouvernorat <span class="text-red-500">*</span>
                </label>
                <select name="state" id="edit_state" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Tunis">Tunis</option>
                    <option value="Ariana">Ariana</option>
                    <option value="Ben Arous">Ben Arous</option>
                    <option value="Manouba">Manouba</option>
                    <option value="Nabeul">Nabeul</option>
                    <option value="Zaghouan">Zaghouan</option>
                    <option value="Bizerte">Bizerte</option>
                    <option value="Béja">Béja</option>
                    <option value="Jendouba">Jendouba</option>
                    <option value="Kef">Kef</option>
                    <option value="Siliana">Siliana</option>
                    <option value="Sousse">Sousse</option>
                    <option value="Monastir">Monastir</option>
                    <option value="Mahdia">Mahdia</option>
                    <option value="Sfax">Sfax</option>
                    <option value="Kairouan">Kairouan</option>
                    <option value="Kasserine">Kasserine</option>
                    <option value="Sidi Bouzid">Sidi Bouzid</option>
                    <option value="Gabès">Gabès</option>
                    <option value="Médenine">Médenine</option>
                    <option value="Tataouine">Tataouine</option>
                    <option value="Gafsa">Gafsa</option>
                    <option value="Tozeur">Tozeur</option>
                    <option value="Kébili">Kébili</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button"
                        onclick="document.getElementById('edit-address-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(address) {
    const modal = document.getElementById('edit-address-modal');
    const form = document.getElementById('edit-address-form');

    form.action = `/profile/addresses/${address.id}`;
    document.getElementById('edit_label').value = address.label;
    document.getElementById('edit_type').value = address.type;
    document.getElementById('edit_full_name').value = address.full_name;
    document.getElementById('edit_phone').value = address.phone;
    document.getElementById('edit_address_line1').value = address.address_line1;
    document.getElementById('edit_address_line2').value = address.address_line2 || '';
    document.getElementById('edit_city').value = address.city;
    document.getElementById('edit_postal_code').value = address.postal_code;
    document.getElementById('edit_state').value = address.state;

    modal.classList.remove('hidden');
}
</script>
@endsection
