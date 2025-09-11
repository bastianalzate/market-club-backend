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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('wompi_transaction_id')->unique();
            $table->string('reference')->unique();
            $table->string('payment_method')->default('CARD');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('COP');
            $table->string('status')->default('PENDING');
            $table->string('wompi_status')->nullable();
            $table->json('wompi_response')->nullable();
            $table->json('customer_data')->nullable();
            $table->string('payment_url')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('wompi_transaction_id');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};