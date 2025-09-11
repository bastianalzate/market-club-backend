<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Database\Seeder;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener órdenes existentes
        $orders = Order::with('user')->get();

        if ($orders->isEmpty()) {
            $this->command->info('No hay órdenes para crear transacciones de pago.');
            return;
        }

        $paymentMethods = ['CARD', 'PSE', 'NEQUI'];
        $statuses = ['APPROVED', 'PENDING', 'DECLINED'];
        $wompiStatuses = ['APPROVED', 'PENDING', 'DECLINED'];

        foreach ($orders as $order) {
            // Crear transacción de pago para cada orden
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $status = $statuses[array_rand($statuses)];
            $wompiStatus = $wompiStatuses[array_rand($wompiStatuses)];

            PaymentTransaction::create([
                'order_id' => $order->id,
                'wompi_transaction_id' => 'wompi_' . uniqid(),
                'reference' => 'ORDER_' . $order->id . '_' . time(),
                'payment_method' => $paymentMethod,
                'amount' => $order->total_amount,
                'currency' => 'COP',
                'status' => $status,
                'wompi_status' => $wompiStatus,
                'wompi_response' => [
                    'id' => 'wompi_' . uniqid(),
                    'status' => $wompiStatus,
                    'amount_in_cents' => (int) ($order->total_amount * 100),
                    'currency' => 'COP',
                    'reference' => 'ORDER_' . $order->id . '_' . time(),
                    'payment_method' => [
                        'type' => $paymentMethod,
                        'installments' => 1,
                    ],
                    'created_at' => now()->toISOString(),
                ],
                'customer_data' => [
                    'email' => $order->user->email,
                    'full_name' => $order->user->name,
                    'phone_number' => $order->user->phone ?? '',
                ],
                'payment_url' => $status === 'PENDING' ? 'https://checkout.wompi.co/l/checkout_' . uniqid() : null,
                'processed_at' => $status === 'APPROVED' ? now() : null,
            ]);

            // Actualizar estado de la orden según el estado del pago
            if ($status === 'APPROVED') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
            } elseif ($status === 'DECLINED') {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }
        }

        $this->command->info('Transacciones de pago creadas exitosamente.');
    }
}