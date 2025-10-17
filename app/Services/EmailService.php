<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Wholesaler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
     * Enviar email de habilitación de mayorista para un usuario (is_wholesaler = true)
     */
    public function sendWholesalerActivationEmailForUser(User $user): bool
    {
        try {
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
                Log::info('Wholesaler activation email (User) sent successfully via Brevo', [
                    'user_id' => $user->id,
                    'email' => $user->email,
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
}
