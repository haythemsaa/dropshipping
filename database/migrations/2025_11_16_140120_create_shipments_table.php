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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');

            $table->string('tracking_number')->unique()->nullable();
            $table->string('carrier')->nullable(); // La Poste, Aramex, Rapid-Post, etc.

            $table->enum('status', [
                'pending', 'preparing', 'shipped',
                'in_transit', 'out_for_delivery', 'delivered',
                'failed', 'returned'
            ])->default('pending');

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery')->nullable();

            $table->text('shipping_notes')->nullable();
            $table->text('tracking_history')->nullable(); // JSON for tracking updates

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
