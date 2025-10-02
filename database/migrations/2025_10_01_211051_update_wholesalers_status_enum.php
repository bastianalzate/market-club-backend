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
        // Primero crear una columna temporal
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->enum('status_temp', ['enabled', 'disabled'])->default('disabled')->after('status');
        });
        
        // Migrar los datos
        DB::statement("UPDATE wholesalers SET status_temp = 'enabled' WHERE status = 'active'");
        DB::statement("UPDATE wholesalers SET status_temp = 'disabled' WHERE status = 'inactive'");
        DB::statement("UPDATE wholesalers SET status_temp = 'disabled' WHERE status = 'pending_approval'");
        
        // Eliminar la columna original y renombrar la temporal
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->renameColumn('status_temp', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Crear columna temporal con los valores originales
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->enum('status_temp', ['active', 'inactive', 'pending_approval'])->default('pending_approval')->after('status');
        });
        
        // Migrar los datos de vuelta
        DB::statement("UPDATE wholesalers SET status_temp = 'active' WHERE status = 'enabled'");
        DB::statement("UPDATE wholesalers SET status_temp = 'inactive' WHERE status = 'disabled'");
        
        // Eliminar la columna actual y renombrar la temporal
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('wholesalers', function (Blueprint $table) {
            $table->renameColumn('status_temp', 'status');
        });
    }
};
