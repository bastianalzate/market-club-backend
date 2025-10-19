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
            $loginUrl = rtrim($appUrl, '/');

            $safeName = htmlspecialchars($user->name ?? '');

            $htmlContent = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Cuenta de Mayorista Activada - Market Club</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { 
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                        line-height: 1.6; 
                        color: #374151; 
                        background-color: #f8fafc;
                    }
                    .email-container { 
                        max-width: 500px; 
                        margin: 0 auto; 
                        background-color: #ffffff;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .header { 
                        background: linear-gradient(135deg, #B48C2B 0%, #D4A843 100%);
                        color: white; 
                        padding: 25px 20px; 
                        text-align: center;
                    }
                    .header h1 { 
                        font-size: 22px; 
                        font-weight: 700; 
                        margin-bottom: 5px;
                    }
                    .header p {
                        font-size: 14px;
                        opacity: 0.9;
                    }
                    .content { 
                        padding: 25px 20px; 
                        background-color: #ffffff;
                    }
                    .greeting {
                        font-size: 16px;
                        color: #1f2937;
                        margin-bottom: 15px;
                        font-weight: 600;
                    }
                    .cta-button {
                        display: inline-block;
                        background: linear-gradient(135deg, #B48C2B 0%, #D4A843 100%);
                        color: #ffffff !important;
                        padding: 12px 25px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: 600;
                        font-size: 14px;
                        margin: 15px 0;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .cta-section {
                        text-align: center;
                        margin: 20px 0;
                        padding: 20px;
                        background: linear-gradient(135deg, #fef9e7 0%, #fdf4d3 100%);
                        border-radius: 8px;
                        border: 1px solid #f4d03f;
                    }
                    .cta-section p {
                        font-size: 14px;
                        color: #8B6914;
                        margin-bottom: 10px;
                    }
                    .footer { 
                        background-color: #B48C2B; 
                        color: #ffffff; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 12px;
                    }
                    .footer .brand {
                        color: #ffffff;
                        font-weight: 600;
                        font-size: 14px;
                        margin-bottom: 5px;
                    }
                    .footer p {
                        margin: 3px 0;
                        opacity: 0.9;
                    }
                    @media (max-width: 500px) {
                        .email-container { margin: 0; }
                        .header, .content, .footer { padding: 15px; }
                        .header h1 { font-size: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="header">
                        <h1>Bienvenido a Market Club</h1>
                        <p style="color: white;">Tu cuenta de mayorista ha sido activada</p>
                    </div>
                    <div class="content">
                        <div class="greeting">Hola ' . $safeName . ',</div>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Excelentes noticias! Tu cuenta de mayorista ha sido activada y ya puedes acceder a todos nuestros beneficios.</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Tu solicitud ha sido aprobada y tu cuenta está lista para usar.</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;"><strong>Beneficios de ser mayorista:</strong></p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">• Precios especiales de mayorista</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">• Catálogo exclusivo de productos</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">• Gestión de pedidos simplificada</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">• Soporte prioritario</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;"><strong>Tus credenciales de acceso:</strong></p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">Email: ' . htmlspecialchars($user->email) . '</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Contraseña: ' . htmlspecialchars($newPassword) . '</p>
                        
                        <p style="font-size: 14px; color: #991b1b; margin-bottom: 20px; font-weight: 500;">Por seguridad, te recomendamos cambiar esta contraseña después de tu primer inicio de sesión.</p>
                        
                        <div class="cta-section">
                            <p><strong>Comienza a explorar tu nueva cuenta!</strong></p>
                            <a href="' . $loginUrl . '" class="cta-button" style="color: #ffffff !important;">Acceder a mi cuenta</a>
                        </div>
                        
                        <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 20px;">
                            ¿Necesitas ayuda? Contáctanos y te ayudaremos.
                        </p>
                    </div>
                    <div class="footer">
                        <div class="brand">Market Club</div>
                        <p>Tu plataforma de confianza para productos de calidad</p>
                        <p style="font-size: 11px; opacity: 0.8; margin-top: 10px;">Email automático - No responder</p>
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

    /**
     * Enviar email de confirmación de solicitud de mayorista
     */
    public function sendWholesalerApplicationConfirmationEmail(User $user): bool
    {
        try {
            $subject = 'Solicitud de Mayorista Recibida - Market Club';

            $appUrl = env('APP_URL', 'https://marketclub.com');
            $safeName = htmlspecialchars($user->name ?? '');

            $htmlContent = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Solicitud de Mayorista Recibida - Market Club</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { 
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                        line-height: 1.6; 
                        color: #374151; 
                        background-color: #f8fafc;
                    }
                    .email-container { 
                        max-width: 500px; 
                        margin: 0 auto; 
                        background-color: #ffffff;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .header { 
                        background: linear-gradient(135deg, #B48C2B 0%, #D4A843 100%);
                        color: white; 
                        padding: 25px 20px; 
                        text-align: center;
                    }
                    .header h1 { 
                        font-size: 22px; 
                        font-weight: 700; 
                        margin-bottom: 5px;
                    }
                    .header p {
                        font-size: 14px;
                        opacity: 0.9;
                    }
                    .content { 
                        padding: 25px 20px; 
                        background-color: #ffffff;
                    }
                    .greeting {
                        font-size: 16px;
                        color: #1f2937;
                        margin-bottom: 15px;
                        font-weight: 600;
                    }
                    .cta-section {
                        text-align: center;
                        margin: 20px 0;
                        padding: 20px;
                        background: linear-gradient(135deg, #fef9e7 0%, #fdf4d3 100%);
                        border-radius: 8px;
                        border: 1px solid #f4d03f;
                    }
                    .cta-section p {
                        font-size: 14px;
                        color: #8B6914;
                        margin-bottom: 10px;
                    }
                    .footer { 
                        background-color: #B48C2B; 
                        color: #ffffff; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 12px;
                    }
                    .footer .brand {
                        color: #ffffff;
                        font-weight: 600;
                        font-size: 14px;
                        margin-bottom: 5px;
                    }
                    .footer p {
                        margin: 3px 0;
                        opacity: 0.9;
                    }
                    @media (max-width: 500px) {
                        .email-container { margin: 0; }
                        .header, .content, .footer { padding: 15px; }
                        .header h1 { font-size: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="header">
                        <h1>Solicitud Recibida</h1>
                        <p>Tu solicitud de mayorista ha sido procesada</p>
                    </div>
                    <div class="content">
                        <div class="greeting">Hola ' . $safeName . ',</div>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Gracias por tu interés en convertirte en mayorista de Market Club.</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Hemos recibido tu solicitud y nuestro equipo la está revisando.</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;"><strong>Información de tu solicitud:</strong></p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">Email: ' . htmlspecialchars($user->email) . '</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">NIT: ' . htmlspecialchars($user->nit ?? 'No especificado') . '</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;">Fecha: ' . now()->format('d/m/Y H:i') . '</p>
                        
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 15px;"><strong>¿Qué sigue ahora?</strong></p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">• Revisaremos tu solicitud y documentos</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 5px;">• Te contactaremos en 2-3 días hábiles</p>
                        <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">• Recibirás tus credenciales al ser aprobado</p>
                        
                        <div class="cta-section">
                            <p><strong>¿Tienes preguntas?</strong></p>
                            <p>Contáctanos y te ayudaremos con cualquier duda sobre tu solicitud.</p>
                        </div>
                    </div>
                    <div class="footer">
                        <div class="brand">Market Club</div>
                        <p>Tu plataforma de confianza para productos de calidad</p>
                        <p style="font-size: 11px; opacity: 0.8; margin-top: 10px;">Email automático - No responder</p>
                    </div>
                </div>
            </body>
            </html>';

            $textContent = "
¡Solicitud de Mayorista Recibida!

Hola {$user->name},

¡Gracias por tu interés en convertirte en mayorista de Market Club!

📋 TU SOLICITUD HA SIDO RECIBIDA
Hemos recibido tu solicitud para convertirte en mayorista y estamos revisando tu información.

¿QUÉ SIGUE AHORA?
- Nuestro equipo revisará tu solicitud y documentos
- Te contactaremos en un plazo de 2-3 días hábiles
- Una vez aprobada, recibirás tus credenciales de acceso
- Podrás acceder a precios especiales y catálogo exclusivo

INFORMACIÓN DE TU SOLICITUD:
- Nombre: {$user->name}
- Email: {$user->email}
- NIT: " . ($user->nit ?? 'No especificado') . "
- País: " . ($user->country ?? 'No especificado') . "
- Fecha de solicitud: " . now()->format('d/m/Y H:i') . "

Si tienes alguna pregunta o necesitas información adicional, no dudes en contactarnos.

¡Gracias por elegir Market Club!

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
                Log::info('Wholesaler application confirmation email sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Wholesaler application confirmation email error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null
            ]);
            return false;
        }
    }

    /**
     * Enviar email de confirmación de suscripción
     */
    public function sendSubscriptionConfirmation($subscription): bool
    {
        try {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;
            
            $subject = "¡Bienvenido al Club de Socios Market Club! 🎉";
            
            $htmlContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='margin: 0; font-size: 28px;'>¡Bienvenido al Club de Socios!</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Tu suscripción ha sido activada exitosamente</p>
                </div>
                
                <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #333; margin-top: 0;'>Detalles de tu suscripción:</h2>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;'>
                        <h3 style='margin: 0 0 10px 0; color: #667eea;'>{$plan->name}</h3>
                        <p style='margin: 5px 0; color: #666;'><strong>Precio:</strong> $" . number_format($plan->price, 0, ',', '.') . " COP/mes</p>
                        <p style='margin: 5px 0; color: #666;'><strong>Válida hasta:</strong> " . $subscription->ends_at->format('d/m/Y') . "</p>
                        <p style='margin: 5px 0; color: #666;'><strong>Renovación automática:</strong> " . ($subscription->auto_renew ? 'Activada' : 'Desactivada') . "</p>
                    </div>
                    
                    <h3 style='color: #333;'>Beneficios de tu membresía:</h3>
                    <ul style='color: #666; line-height: 1.6;'>
                        <li>Descuentos exclusivos en todos los productos</li>
                        <li>Acceso prioritario a nuevos lanzamientos</li>
                        <li>Envío gratuito en todas las compras</li>
                        <li>Asesoría personalizada de productos</li>
                        <li>Eventos y promociones especiales</li>
                    </ul>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . config('app.frontend_url') . "/club-socios' 
                           style='background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold;'>
                            Ver mi membresía
                        </a>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 20px; color: #666; font-size: 14px;'>
                    <p>¡Gracias por ser parte del Club de Socios Market Club!</p>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
            ";

            $textContent = "
¡Bienvenido al Club de Socios Market Club!

Tu suscripción ha sido activada exitosamente.

Detalles de tu suscripción:
- Plan: {$plan->name}
- Precio: $" . number_format($plan->price, 0, ',', '.') . " COP/mes
- Válida hasta: " . $subscription->ends_at->format('d/m/Y') . "
- Renovación automática: " . ($subscription->auto_renew ? 'Activada' : 'Desactivada') . "

Beneficios de tu membresía:
- Descuentos exclusivos en todos los productos
- Acceso prioritario a nuevos lanzamientos
- Envío gratuito en todas las compras
- Asesoría personalizada de productos
- Eventos y promociones especiales

¡Gracias por ser parte del Club de Socios Market Club!

Este es un email automático, por favor no respondas a este mensaje.
            ";

            $result = $this->brevoService->sendEmail(
                [$user->email => ($user->name ?? 'Usuario')],
                $subject,
                $htmlContent,
                $textContent
            );

            if ($result) {
                Log::info('Subscription confirmation email sent successfully', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'email' => $user->email
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Subscription confirmation email error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'subscription_id' => $subscription->id ?? null,
                'email' => $user->email ?? null
            ]);
            return false;
        }
    }

    /**
     * Enviar email de advertencia de vencimiento de suscripción
     */
    public function sendSubscriptionExpirationWarning($subscription, $days): bool
    {
        try {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;
            
            $subject = "⚠️ Tu suscripción al Club de Socios expira en {$days} días";
            
            $htmlContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='margin: 0; font-size: 28px;'>⚠️ Tu suscripción expira pronto</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>No pierdas los beneficios de tu membresía</p>
                </div>
                
                <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #333; margin-top: 0;'>Tu suscripción expira en {$days} días</h2>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff6b6b;'>
                        <h3 style='margin: 0 0 10px 0; color: #ff6b6b;'>{$plan->name}</h3>
                        <p style='margin: 5px 0; color: #666;'><strong>Expira el:</strong> " . $subscription->ends_at->format('d/m/Y') . "</p>
                        <p style='margin: 5px 0; color: #666;'><strong>Renovación automática:</strong> " . ($subscription->auto_renew ? 'Activada' : 'Desactivada') . "</p>
                    </div>
                    
                    " . ($subscription->auto_renew ? "
                    <div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <strong>✅ Renovación automática activada</strong><br>
                        Tu suscripción se renovará automáticamente el " . $subscription->ends_at->format('d/m/Y') . ". 
                        No necesitas hacer nada.
                    </div>
                    " : "
                    <div style='background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <strong>⚠️ Renovación automática desactivada</strong><br>
                        Para continuar disfrutando de los beneficios, necesitas renovar tu suscripción manualmente.
                    </div>
                    ") . "
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . config('app.frontend_url') . "/club-socios' 
                           style='background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold;'>
                            Gestionar mi suscripción
                        </a>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 20px; color: #666; font-size: 14px;'>
                    <p>¡No pierdas los beneficios exclusivos del Club de Socios!</p>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
            ";

            $textContent = "
⚠️ Tu suscripción al Club de Socios expira en {$days} días

Tu suscripción expira en {$days} días:
- Plan: {$plan->name}
- Expira el: " . $subscription->ends_at->format('d/m/Y') . "
- Renovación automática: " . ($subscription->auto_renew ? 'Activada' : 'Desactivada') . "

" . ($subscription->auto_renew ? 
"✅ Renovación automática activada
Tu suscripción se renovará automáticamente el " . $subscription->ends_at->format('d/m/Y') . ". No necesitas hacer nada." : 
"⚠️ Renovación automática desactivada
Para continuar disfrutando de los beneficios, necesitas renovar tu suscripción manualmente.") . "

¡No pierdas los beneficios exclusivos del Club de Socios!

Este es un email automático, por favor no respondas a este mensaje.
            ";

            $result = $this->brevoService->sendEmail(
                [$user->email => ($user->name ?? 'Usuario')],
                $subject,
                $htmlContent,
                $textContent
            );

            if ($result) {
                Log::info('Subscription expiration warning email sent successfully', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'email' => $user->email,
                    'days_remaining' => $days
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Subscription expiration warning email error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'subscription_id' => $subscription->id ?? null,
                'email' => $user->email ?? null
            ]);
            return false;
        }
    }

    /**
     * Enviar email de confirmación de renovación de suscripción
     */
    public function sendSubscriptionRenewalConfirmation($subscription): bool
    {
        try {
            $user = $subscription->user;
            $plan = $subscription->subscriptionPlan;
            
            $subject = "✅ Tu suscripción al Club de Socios ha sido renovada";
            
            $htmlContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(135deg, #00b894 0%, #00a085 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='margin: 0; font-size: 28px;'>✅ Suscripción renovada</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Tu membresía ha sido renovada exitosamente</p>
                </div>
                
                <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #333; margin-top: 0;'>Detalles de la renovación:</h2>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #00b894;'>
                        <h3 style='margin: 0 0 10px 0; color: #00b894;'>{$plan->name}</h3>
                        <p style='margin: 5px 0; color: #666;'><strong>Precio pagado:</strong> $" . number_format($plan->price, 0, ',', '.') . " COP</p>
                        <p style='margin: 5px 0; color: #666;'><strong>Nueva fecha de vencimiento:</strong> " . $subscription->ends_at->format('d/m/Y') . "</p>
                        <p style='margin: 5px 0; color: #666;'><strong>Próxima renovación:</strong> " . $subscription->next_billing_date->format('d/m/Y') . "</p>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . config('app.frontend_url') . "/club-socios' 
                           style='background: #00b894; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold;'>
                            Ver mi membresía
                        </a>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 20px; color: #666; font-size: 14px;'>
                    <p>¡Gracias por continuar siendo parte del Club de Socios Market Club!</p>
                    <p>Este es un email automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
            ";

            $textContent = "
✅ Tu suscripción al Club de Socios ha sido renovada

Tu membresía ha sido renovada exitosamente.

Detalles de la renovación:
- Plan: {$plan->name}
- Precio pagado: $" . number_format($plan->price, 0, ',', '.') . " COP
- Nueva fecha de vencimiento: " . $subscription->ends_at->format('d/m/Y') . "
- Próxima renovación: " . $subscription->next_billing_date->format('d/m/Y') . "

¡Gracias por continuar siendo parte del Club de Socios Market Club!

Este es un email automático, por favor no respondas a este mensaje.
            ";

            $result = $this->brevoService->sendEmail(
                [$user->email => ($user->name ?? 'Usuario')],
                $subject,
                $htmlContent,
                $textContent
            );

            if ($result) {
                Log::info('Subscription renewal confirmation email sent successfully', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'email' => $user->email
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Subscription renewal confirmation email error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'subscription_id' => $subscription->id ?? null,
                'email' => $user->email ?? null
            ]);
            return false;
        }
    }
}
