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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Curioso Cervecero, Coleccionista Cervecero, Maestro Cervecero
            $table->string('slug')->unique(); // curioso-cervecero, coleccionista-cervecero, maestro-cervecero
            $table->text('description');
            $table->decimal('price', 10, 2); // Precio mensual
            $table->json('features'); // Lista de características del plan
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Para ordenar los planes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
