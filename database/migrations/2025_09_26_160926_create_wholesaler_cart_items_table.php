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
        Schema::create('wholesaler_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wholesaler_cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->json('product_snapshot')->nullable(); // Snapshot del producto al momento de agregar
            $table->boolean('is_wholesaler_item')->default(true);
            $table->text('notes')->nullable(); // Notas específicas del item
            $table->timestamps();

            $table->index(['wholesaler_cart_id', 'product_id']);
            $table->unique(['wholesaler_cart_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesaler_cart_items');
    }
};
