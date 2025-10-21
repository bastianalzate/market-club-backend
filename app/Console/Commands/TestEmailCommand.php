<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Order;
use App\Models\User;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email sending functionality';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email sending to: {$email}");
        
        try {
            // Crear usuario de prueba
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Usuario de Prueba',
                    'email' => $email,
                    'password' => bcrypt('password'),
                    'phone' => '3001234567'
                ]
            );
            
            // Crear orden de prueba
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . time(),
                'subtotal' => 10000,
                'tax_amount' => 1900,
                'shipping_amount' => 5000,
                'total_amount' => 16900,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => [
                    'name' => 'Usuario de Prueba',
                    'address' => 'Dirección de Prueba',
                    'city' => 'Ciudad',
                    'state' => 'Estado',
                    'postal_code' => '12345',
                    'country' => 'Colombia',
                    'phone' => '3001234567'
                ],
                'billing_address' => [
                    'name' => 'Usuario de Prueba',
                    'address' => 'Dirección de Facturación',
                    'city' => 'Ciudad',
                    'state' => 'Estado',
                    'postal_code' => '12345',
                    'country' => 'Colombia',
                    'phone' => '3001234567'
                ]
            ]);
            
            // Enviar email
            $emailService = app(EmailService::class);
            $result = $emailService->sendOrderConfirmation($order);
            
            if ($result) {
                $this->info("✅ Email enviado exitosamente!");
            } else {
                $this->error("❌ Error al enviar email");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
