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
        Schema::create('wholesaler_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wholesaler_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // Para mayoristas no autenticados
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->json('metadata')->nullable(); // Para datos adicionales
            $table->text('notes')->nullable(); // Notas del mayorista
            $table->timestamps();

            $table->index(['wholesaler_id', 'session_id']);
            $table->unique(['wholesaler_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesaler_carts');
    }
};
