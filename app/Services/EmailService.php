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

    /**
     * Enviar email de renovación de suscripción exitosa
     */
    public function sendSubscriptionRenewalSuccessEmail($user, $subscription, $transaction): bool
    {
        try {
            $plan = $subscription->subscriptionPlan;
            
            $data = [
                'user' => $user,
                'subscription' => $subscription,
                'plan' => $plan,
                'transaction' => $transaction,
                'amount' => $plan->price,
                'next_billing_date' => $subscription->next_billing_date->format('d/m/Y'),
            ];

            Mail::send('emails.subscription-renewal-success', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Suscripción Renovada Exitosamente - Market Club');
            });

            Log::info("Subscription renewal success email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Subscription renewal success email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de fallo de pago
     */
    public function sendPaymentFailedEmail($user, $subscription, string $errorMessage): bool
    {
        try {
            $plan = $subscription->subscriptionPlan;
            $retryCount = $subscription->payment_retry_count;
            $maxRetries = 4;
            $retriesLeft = $maxRetries - $retryCount;

            $data = [
                'user' => $user,
                'subscription' => $subscription,
                'plan' => $plan,
                'error_message' => $errorMessage,
                'retry_count' => $retryCount,
                'retries_left' => $retriesLeft,
                'payment_method' => $subscription->masked_payment_method,
            ];

            Mail::send('emails.payment-failed', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('⚠️ Fallo en el Pago de tu Suscripción - Market Club');
            });

            Log::info("Payment failed email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Payment failed email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de suscripción suspendida
     */
    public function sendSubscriptionSuspendedEmail($user, $subscription): bool
    {
        try {
            $plan = $subscription->subscriptionPlan;

            $data = [
                'user' => $user,
                'subscription' => $subscription,
                'plan' => $plan,
                'suspended_at' => $subscription->suspended_at->format('d/m/Y H:i'),
            ];

            Mail::send('emails.subscription-suspended', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('🔒 Suscripción Suspendida - Market Club');
            });

            Log::info("Subscription suspended email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Subscription suspended email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email recordatorio antes del cobro
     */
    public function sendUpcomingBillingReminderEmail($user, $subscription): bool
    {
        try {
            $plan = $subscription->subscriptionPlan;
            $daysUntilBilling = now()->diffInDays($subscription->next_billing_date);

            $data = [
                'user' => $user,
                'subscription' => $subscription,
                'plan' => $plan,
                'amount' => $plan->price,
                'billing_date' => $subscription->next_billing_date->format('d/m/Y'),
                'days_until_billing' => $daysUntilBilling,
                'payment_method' => $subscription->masked_payment_method,
            ];

            Mail::send('emails.upcoming-billing-reminder', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Recordatorio: Próximo Cobro de Suscripción - Market Club');
            });

            Log::info("Upcoming billing reminder email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Upcoming billing reminder email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar resumen de renovaciones al administrador
     */
    public function sendAdminRenewalSummaryEmail(string $adminEmail, array $summary): bool
    {
        try {
            Mail::send('emails.admin-renewal-summary', [
                'summary' => $summary,
            ], function ($message) use ($adminEmail, $summary) {
                $message->to($adminEmail)
                    ->subject("Resumen de Renovaciones - {$summary['date']}");
            });

            Log::info("Admin renewal summary email sent");
            return true;

        } catch (\Exception $e) {
            Log::error('Admin renewal summary email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de confirmación de cambio de método de pago
     */
    public function sendPaymentMethodUpdatedEmail($user, $subscription): bool
    {
        try {
            $data = [
                'user' => $user,
                'subscription' => $subscription,
                'plan' => $subscription->subscriptionPlan,
                'payment_method' => $subscription->masked_payment_method,
                'updated_at' => now()->format('d/m/Y H:i'),
            ];

            Mail::send('emails.payment-method-updated', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Método de Pago Actualizado - Market Club');
            });

            Log::info("Payment method updated email sent to user {$user->id}");
            return true;

        } catch (\Exception $e) {
            Log::error('Payment method updated email error: ' . $e->getMessage());
            return false;
        }
    }
}
