@extends('layouts.dashboard')

@section('title', 'Statistiques du coupon')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.coupons.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $coupon->name }}</h1>
                    <div class="flex items-center gap-4">
                        <span class="font-mono text-lg font-bold text-indigo-600">{{ $coupon->code }}</span>
                        @if($coupon->is_active && $coupon->valid_until->isFuture())
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Actif</span>
                        @elseif($coupon->valid_until->isPast())
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Expiré</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactif</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Modifier
                    </a>
                </div>
            </div>

            @if($coupon->description)
                <p class="text-gray-600 mb-4">{{ $coupon->description }}</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div>
                    <div class="text-sm text-gray-600">Type</div>
                    <div class="font-medium">
                        @if($coupon->type === 'percentage')
                            Pourcentage ({{ $coupon->value }}%)
                        @elseif($coupon->type === 'fixed')
                            Montant fixe ({{ number_format($coupon->value, 2) }} TND)
                        @else
                            Livraison gratuite
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Période</div>
                    <div class="font-medium text-sm">
                        {{ $coupon->valid_from->format('d/m/Y') }}<br>
                        → {{ $coupon->valid_until->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Montant minimum</div>
                    <div class="font-medium">
                        @if($coupon->min_order_amount)
                            {{ number_format($coupon->min_order_amount, 2) }} TND
                        @else
                            Aucun
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Plafond réduction</div>
                    <div class="font-medium">
                        @if($coupon->max_discount_amount)
                            {{ number_format($coupon->max_discount_amount, 2) }} TND
                        @else
                            Aucun
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Utilisations totales</div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_uses'] }}</div>
                @if($coupon->usage_limit)
                    <div class="text-xs text-gray-500 mt-1">/ {{ $coupon->usage_limit }} max</div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Réduction totale</div>
                <div class="text-3xl font-bold text-green-600">{{ number_format($stats['total_discount'], 2) }}</div>
                <div class="text-xs text-gray-500 mt-1">TND</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Utilisateurs uniques</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['unique_users'] }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Réduction moyenne</div>
                <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['avg_discount'], 2) }}</div>
                <div class="text-xs text-gray-500 mt-1">TND</div>
            </div>
        </div>

        <!-- Usage History -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Historique d'utilisation</h2>
            </div>

            @if($usages->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Commande</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Réduction</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($usages as $usage)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $usage->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $usage->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $usage->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $usage->order) }}" class="text-indigo-600 hover:text-indigo-900 font-mono">
                                        {{ $usage->order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600">
                                    -{{ number_format($usage->discount_amount, 2) }} TND
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($usages->hasPages())
                    <div class="px-6 py-4 bg-gray-50">
                        {{ $usages->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p>Ce coupon n'a pas encore été utilisé</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
