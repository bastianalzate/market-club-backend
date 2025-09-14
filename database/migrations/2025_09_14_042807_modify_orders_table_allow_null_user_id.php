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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Drop existing foreign key
            $table->foreignId('user_id')->nullable()->change(); // Make nullable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Re-add with set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->change(); // Revert to non-nullable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Re-add original
        });
    }
};
