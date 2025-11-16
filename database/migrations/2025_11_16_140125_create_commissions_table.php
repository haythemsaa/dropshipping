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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            $table->decimal('product_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2); // Montant total de l'item

            $table->decimal('commission_rate', 5, 2); // Taux de commission (%)
            $table->decimal('commission_amount', 10, 2); // Montant de la commission
            $table->decimal('supplier_amount', 10, 2); // Montant revenant au fournisseur

            $table->enum('status', ['pending', 'approved', 'paid', 'disputed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
