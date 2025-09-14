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
        Schema::table('carts', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea existente
            $table->dropForeign(['user_id']);
            
            // Modificar la columna para permitir NULL
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Recrear la clave foránea con onDelete('set null')
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Eliminar la clave foránea
            $table->dropForeign(['user_id']);
            
            // Eliminar registros con user_id NULL antes de cambiar la columna
            DB::table('carts')->whereNull('user_id')->delete();
            
            // Modificar la columna para no permitir NULL
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            
            // Recrear la clave foránea original
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};