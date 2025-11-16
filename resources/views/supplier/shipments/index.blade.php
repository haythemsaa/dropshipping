@extends('layouts.dashboard')

@section('title', 'Gestion des expéditions - Fournisseur')

@section('header', 'Gestion des expéditions')

@section('sidebar')
    <a href="{{ route('supplier.dashboard') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Tableau de bord
    </a>
    <a href="{{ route('supplier.products.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Mes produits
    </a>
    <a href="{{ route('supplier.orders.index') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commandes
    </a>
    <a href="{{ route('supplier.shipments.index') }}"
       class="block px-6 py-3 text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600 font-medium">
        Expéditions
    </a>
    <a href="{{ route('supplier.commissions') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Commissions
    </a>
    <a href="{{ route('supplier.statistics') }}"
       class="block px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
        Statistiques
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <p class="text-sm text-gray-600">Suivez et gérez toutes vos expéditions</p>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-xs text-gray-600">Total expéditions</p>
            <p class="text-2xl font-bold text-gray-900">{{ $statusCounts['all'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-xs text-gray-600">En préparation</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $statusCounts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-xs text-gray-600">Expédiées</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statusCounts['shipped'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-xs text-gray-600">Livrées</p>
            <p class="text-2xl font-bold text-green-600">{{ $statusCounts['delivered'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('supplier.shipments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Numéro tracking, commande..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" id="status"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En préparation</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Expédiées</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>En transit</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livrées</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échec livraison</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Shipments Table -->
    @if($shipments->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Tracking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transporteur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date expédition</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shipments as $shipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $shipment->tracking_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('supplier.orders.show', $shipment->order) }}"
                                   class="text-sm text-indigo-600 hover:text-indigo-900">
                                    #{{ $shipment->order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $shipment->order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $shipment->order->shipping_city }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $shipment->carrier }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($shipment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($shipment->status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($shipment->status === 'in_transit') bg-indigo-100 text-indigo-800
                                    @elseif($shipment->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($shipment->status === 'failed') bg-red-100 text-red-800
                                    @endif">
                                    @if($shipment->status === 'pending') En préparation
                                    @elseif($shipment->status === 'shipped') Expédiée
                                    @elseif($shipment->status === 'in_transit') En transit
                                    @elseif($shipment->status === 'delivered') Livrée
                                    @elseif($shipment->status === 'failed') Échec
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($shipment->shipped_at)
                                    {{ $shipment->shipped_at->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button"
                                        onclick="document.getElementById('update-form-{{ $shipment->id }}').classList.toggle('hidden')"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Mettre à jour
                                </button>
                            </td>
                        </tr>

                        <!-- Update Form (hidden by default) -->
                        <tr id="update-form-{{ $shipment->id }}" class="hidden bg-gray-50">
                            <td colspan="7" class="px-6 py-4">
                                <form action="{{ route('supplier.shipments.update', $shipment) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="status-{{ $shipment->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                Statut
                                            </label>
                                            <select name="status" id="status-{{ $shipment->id }}" required
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>En préparation</option>
                                                <option value="shipped" {{ $shipment->status === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                                                <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>En transit</option>
                                                <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Livrée</option>
                                                <option value="failed" {{ $shipment->status === 'failed' ? 'selected' : '' }}>Échec livraison</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="tracking_number-{{ $shipment->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                N° Tracking
                                            </label>
                                            <input type="text" name="tracking_number" id="tracking_number-{{ $shipment->id }}"
                                                   value="{{ $shipment->tracking_number }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <div>
                                            <label for="carrier-{{ $shipment->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                Transporteur
                                            </label>
                                            <input type="text" name="carrier" id="carrier-{{ $shipment->id }}"
                                                   value="{{ $shipment->carrier }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="notes-{{ $shipment->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            Notes
                                        </label>
                                        <textarea name="notes" id="notes-{{ $shipment->id }}" rows="2"
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $shipment->notes }}</textarea>
                                    </div>

                                    <div class="flex items-center justify-end space-x-3">
                                        <button type="button"
                                                onclick="document.getElementById('update-form-{{ $shipment->id }}').classList.add('hidden')"
                                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                            Enregistrer
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $shipments->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune expédition trouvée</h3>
            <p class="mt-2 text-sm text-gray-500">Les expéditions apparaîtront ici une fois créées.</p>
        </div>
    @endif
@endsection
