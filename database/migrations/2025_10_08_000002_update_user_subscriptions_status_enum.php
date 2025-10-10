<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, cambiar temporalmente la columna a string
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'");
        
        // Ahora actualizar el enum con el nuevo valor 'suspended'
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('active', 'inactive', 'cancelled', 'expired', 'suspended') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero, convertir todas las suscripciones suspendidas a canceladas
        DB::table('user_subscriptions')
            ->where('status', 'suspended')
            ->update(['status' => 'cancelled']);
        
        // Luego restaurar el enum original
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('active', 'inactive', 'cancelled', 'expired') NOT NULL DEFAULT 'active'");
    }
};

