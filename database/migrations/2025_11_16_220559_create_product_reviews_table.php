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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();

            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->string('title', 200)->nullable();
            $table->text('comment')->nullable();

            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->integer('helpful_count')->unsigned()->default(0);

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Index pour les requêtes courantes
            $table->index(['product_id', 'is_approved']);
            $table->index(['user_id', 'created_at']);

            // Empêcher les doublons (un avis par produit par commande)
            $table->unique(['product_id', 'user_id', 'order_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
