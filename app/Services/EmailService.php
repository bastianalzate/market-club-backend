<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Enviar email de confirmación de orden
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            $user = $order->user;
            
            Mail::send('emails.order-confirmation', [
                'order' => $order,
                'user' => $user,
                'items' => $order->orderItems,
            ], function ($message) use ($user, $order) {
                $message->to($user->email, $user->name)
                    ->subject('Confirmación de Orden #' . $order->order_number);
            });

            Log::info("Order confirmation email sent for order {$order->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Order confirmation email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de confirmación de pago
     */
    public function sendPaymentConfirmation(Order $order): bool
    {
        try {
            $user = $order->user;
            
            Mail::send('emails.payment-confirmation', [
                'order' => $order,
                'user' => $user,
                'items' => $order->orderItems,
            ], function ($message) use ($user, $order) {
                $message->to($user->email, $user->name)
                    ->subject('Pago Confirmado - Orden #' . $order->order_number);
            });

            Log::info("Payment confirmation email sent for order {$order->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Payment confirmation email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de cambio de estado de orden
     */
    public function sendOrderStatusUpdate(Order $order, string $oldStatus, string $newStatus): bool
    {
        try {
            $user = $order->user;
            
            Mail::send('emails.order-status-update', [
                'order' => $order,
                'user' => $user,
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'items' => $order->orderItems,
            ], function ($message) use ($user, $order, $newStatus) {
                $message->to($user->email, $user->name)
                    ->subject('Actualización de Orden #' . $order->order_number . ' - ' . ucfirst($newStatus));
            });

            Log::info("Order status update email sent for order {$order->id}: {$oldStatus} -> {$newStatus}");
            return true;

        } catch (\Exception $e) {
            Log::error('Order status update email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de bienvenida
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::send('emails.welcome', [
                'user' => $user,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('¡Bienvenido a Market Club!');
            });

            Log::info("Welcome email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Welcome email error: ' . $e->getMessage());
            return false;
        }
    }
}
