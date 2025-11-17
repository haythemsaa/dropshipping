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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('set null');
            $table->json('variant_attributes')->nullable()->after('variant_id')->comment('Snapshot of variant attributes at order time');
            $table->string('variant_sku')->nullable()->after('variant_attributes')->comment('Snapshot of variant SKU at order time');
            $table->index(['order_id', 'product_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id', 'variant_id']);
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['variant_id', 'variant_attributes', 'variant_sku']);
        });
    }
};
