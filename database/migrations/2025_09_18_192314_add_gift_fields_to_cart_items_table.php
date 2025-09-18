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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('gift_id')->nullable()->after('product_id');
            $table->json('gift_data')->nullable()->after('product_snapshot');
            $table->boolean('is_gift')->default(false)->after('gift_data');
            
            // Hacer product_id nullable para permitir productos especiales
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['gift_id', 'gift_data', 'is_gift']);
            
            // Revertir product_id a no nullable
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
