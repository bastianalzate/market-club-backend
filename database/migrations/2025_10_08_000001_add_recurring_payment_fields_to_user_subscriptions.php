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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Campos para pagos recurrentes
            $table->string('payment_token')->nullable()->after('metadata'); // Token de Wompi para cobros futuros
            $table->string('payment_method_type')->nullable()->after('payment_token'); // CARD, PSE, NEQUI, etc.
            $table->string('last_four_digits')->nullable()->after('payment_method_type'); // Últimos 4 dígitos de tarjeta
            $table->boolean('auto_renew')->default(true)->after('last_four_digits'); // Renovación automática activa
            
            // Campos para manejo de fallos
            $table->integer('payment_retry_count')->default(0)->after('auto_renew'); // Contador de reintentos
            $table->timestamp('last_payment_attempt_at')->nullable()->after('payment_retry_count'); // Última intento de cobro
            $table->text('last_payment_error')->nullable()->after('last_payment_attempt_at'); // Último error de pago
            $table->timestamp('suspended_at')->nullable()->after('cancelled_at'); // Fecha de suspensión por fallo de pago
            
            // Índices para búsquedas eficientes
            $table->index(['auto_renew', 'next_billing_date', 'status']);
            $table->index('payment_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['auto_renew', 'next_billing_date', 'status']);
            $table->dropIndex(['payment_token']);
            
            $table->dropColumn([
                'payment_token',
                'payment_method_type',
                'last_four_digits',
                'auto_renew',
                'payment_retry_count',
                'last_payment_attempt_at',
                'last_payment_error',
                'suspended_at',
            ]);
        });
    }
};

