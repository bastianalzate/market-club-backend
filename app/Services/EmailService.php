<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Wholesaler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmailService
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }
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
     * Enviar email de habilitación de mayorista usando Brevo
     */
    public function sendWholesalerActivationEmail(Wholesaler $wholesaler): bool
    {
        try {
            $result = $this->brevoService->sendWholesalerActivationEmail(
                $wholesaler->email,
                $wholesaler->business_name,
                $wholesaler->contact_name
            );

            if ($result) {
                Log::info("Wholesaler activation email sent successfully via Brevo", [
                    'wholesaler_id' => $wholesaler->id,
                    'email' => $wholesaler->email,
                    'business_name' => $wholesaler->business_name
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Wholesaler activation email error: ' . $e->getMessage(), [
                'wholesaler_id' => $wholesaler->id,
                'email' => $wholesaler->email
            ]);
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
     * Enviar email de habilitación de mayorista para un usuario (is_wholesaler = true)
     */
    public function sendWholesalerActivationEmailForUser(User $user): bool
    {
        try {
            // Generar nueva contraseña para el usuario
            $newPassword = $this->generateAndUpdateUserPassword($user);
            
            $subject = '¡Tu cuenta de mayorista ha sido activada en Market Club!';

            $appUrl = env('APP_URL', 'https://marketclub.com');
            $loginUrl = rtrim($appUrl, '/') . '/mayorista/login';

            $safeName = htmlspecialchars($user->name ?? '');

            $htmlContent = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Cuenta de Mayorista Activada</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #1f2937; color: white; padding: 20px; text-align: center; }
                    .content { padding: 30px; background-color: #f9fafb; }
                    .button { display: inline-block; background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .footer { background-color: #e5e7eb; padding: 20px; text-align: center; font-size: 14px; color: #6b7280; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>¡Bienvenido a Market Club!</h1>
                    </div>
                    <div class="content">
                        <h2>Hola ' . $safeName . ',</h2>
                        <p>¡Excelentes noticias! Tu cuenta de mayorista ha sido activada exitosamente.</p>
                        
                        <p>Ahora puedes acceder a todos los beneficios de nuestro programa de mayoristas:</p>
                        <ul>
                            <li>Precios especiales de mayorista</li>
                            <li>Catálogo exclusivo de productos</li>
                            <li>Gestión de pedidos simplificada</li>
                            <li>Soporte prioritario</li>
                        </ul>
                        
                        <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6;">
                            <h3 style="margin-top: 0; color: #1f2937;">🔐 Tus credenciales de acceso:</h3>
                            <p style="margin: 10px 0;"><strong>Email:</strong> ' . htmlspecialchars($user->email) . '</p>
                            <p style="margin: 10px 0;"><strong>Contraseña temporal:</strong> <code style="background-color: #e5e7eb; padding: 4px 8px; border-radius: 4px; font-family: monospace;">' . htmlspecialchars($newPassword) . '</code></p>
                            <p style="margin: 10px 0; font-size: 14px; color: #6b7280;"><em>Por seguridad, te recomendamos cambiar esta contraseña después de tu primer inicio de sesión.</em></p>
                        </div>
                        
                        <p>Para comenzar a realizar pedidos, simplemente inicia sesión en tu cuenta y navega por nuestro catálogo de productos.</p>
                        
                        <div style="text-align: center;">
                            <a href="' . $loginUrl . '" class="button">Acceder a mi cuenta</a>
                        </div>
                        
                        <p>Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.</p>
                        
                        <p>¡Gracias por ser parte de Market Club!</p>
                    </div>
                    <div class="footer">
                        <p>Market Club - Tu plataforma de confianza para productos de calidad</p>
                        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                    </div>
                </div>
            </body>
            </html>';

            $textContent = "
¡Bienvenido a Market Club!

Hola {$user->name},

¡Excelentes noticias! Tu cuenta de mayorista ha sido activada exitosamente.

Ahora puedes acceder a todos los beneficios de nuestro programa de mayoristas:
- Precios especiales de mayorista
- Catálogo exclusivo de productos
- Gestión de pedidos simplificada
- Soporte prioritario

🔐 TUS CREDENCIALES DE ACCESO:
Email: {$user->email}
Contraseña temporal: {$newPassword}

IMPORTANTE: Por seguridad, te recomendamos cambiar esta contraseña después de tu primer inicio de sesión.

Para comenzar a realizar pedidos, simplemente inicia sesión en tu cuenta y navega por nuestro catálogo de productos.

Acceder a mi cuenta: {$loginUrl}

Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.

¡Gracias por ser parte de Market Club!

Market Club - Tu plataforma de confianza para productos de calidad
Este es un email automático, por favor no respondas a este mensaje.
            ";

            $result = $this->brevoService->sendEmail(
                [$user->email => ($user->name ?? 'Usuario')],
                $subject,
                $htmlContent,
                $textContent
            );

            if ($result) {
                Log::info('Wholesaler activation email (User) sent successfully via Brevo with new password', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'password_generated' => true
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Wholesaler activation email (User) error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
            ]);
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

    /**
     * Generar una contraseña segura aleatoria
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        
        $password = '';
        
        // Asegurar al menos un carácter de cada tipo
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Completar con caracteres aleatorios
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mezclar la contraseña
        return str_shuffle($password);
    }

    /**
     * Generar nueva contraseña para un usuario y actualizarla en la base de datos
     */
    public function generateAndUpdateUserPassword(User $user): string
    {
        $newPassword = $this->generateSecurePassword();
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
        
        Log::info("New password generated for user {$user->id}", [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return $newPassword;
    }
}
