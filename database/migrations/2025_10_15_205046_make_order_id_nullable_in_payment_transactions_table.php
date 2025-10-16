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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Hacer que order_id sea nullable para permitir transacciones de suscripciones
            $table->foreignId('order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Revertir a no nullable (esto puede fallar si hay registros con order_id null)
            $table->foreignId('order_id')->nullable(false)->change();
        });
    }
};