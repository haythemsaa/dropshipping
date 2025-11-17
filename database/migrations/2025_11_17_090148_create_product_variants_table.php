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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique(); // SKU unique pour cette variante
            $table->decimal('price', 10, 2); // Prix de la variante
            $table->decimal('compare_at_price', 10, 2)->nullable(); // Prix barré
            $table->integer('stock_quantity')->default(0);
            $table->json('attributes'); // {"couleur": "Rouge", "taille": "M"}
            $table->json('attribute_value_ids'); // [1, 5] - IDs des values sélectionnées
            $table->string('image_path')->nullable(); // Image spécifique à la variante
            $table->boolean('is_default')->default(false); // Variante par défaut
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index('sku');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
