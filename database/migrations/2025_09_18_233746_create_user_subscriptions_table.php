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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'cancelled', 'expired'])->default('active');
            $table->decimal('price_paid', 10, 2); // Precio que pagó (puede cambiar con el tiempo)
            $table->date('starts_at'); // Fecha de inicio
            $table->date('ends_at'); // Fecha de fin
            $table->date('next_billing_date')->nullable(); // Próxima fecha de facturación
            $table->json('metadata')->nullable(); // Información adicional (método de pago, etc.)
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
