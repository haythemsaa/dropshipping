<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_method')
                ->comment('Gateway de paiement utilisé (edinar, clictopay, konnect)');
            $table->string('transaction_id')->nullable()->after('gateway')
                ->comment('ID de transaction du gateway');
            $table->string('payment_ref')->nullable()->after('transaction_id')
                ->comment('Référence de paiement (pour certains gateways)');
            $table->json('payment_details')->nullable()->after('payment_ref')
                ->comment('Détails supplémentaires du paiement (carte, wallet, etc.)');

            $table->index('transaction_id');
            $table->index('payment_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['payment_ref']);
            $table->dropColumn([
                'gateway',
                'transaction_id',
                'payment_ref',
                'payment_details',
            ]);
        });
    }
};
