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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('is_wholesaler');
            $table->json('address')->nullable()->after('profile_image');
            
            // Configuraciones de notificaciones
            $table->boolean('email_notifications')->default(true)->after('address');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->boolean('order_updates')->default(true)->after('sms_notifications');
            $table->boolean('promotions')->default(true)->after('order_updates');
            $table->boolean('newsletter')->default(true)->after('promotions');
            
            // Configuraciones de privacidad
            $table->string('profile_visibility')->default('private')->after('newsletter');
            $table->boolean('show_orders')->default(false)->after('profile_visibility');
            $table->boolean('show_favorites')->default(false)->after('show_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image',
                'address',
                'email_notifications',
                'sms_notifications',
                'order_updates',
                'promotions',
                'newsletter',
                'profile_visibility',
                'show_orders',
                'show_favorites',
            ]);
        });
    }
};
