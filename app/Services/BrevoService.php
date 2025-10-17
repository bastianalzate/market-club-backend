<?php

namespace App\Services;

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailReplyTo;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('api-key', env('BREVO_API_KEY'));
        
        $this->apiInstance = new TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
    }

    /**
     * Enviar email transaccional usando Brevo
     */
    public function sendEmail(array $to, string $subject, string $htmlContent, string $textContent = null, array $replyTo = null): bool
    {
        try {
            // Preparar destinatarios
            $recipients = [];
            foreach ($to as $email => $name) {
                $recipient = new SendSmtpEmailTo();
                $recipient->setEmail($email);
                $recipient->setName($name);
                $recipients[] = $recipient;
            }

            // Preparar email
            $sendSmtpEmail = new SendSmtpEmail();
            $sendSmtpEmail->setTo($recipients);
            $sendSmtpEmail->setSubject($subject);
            $sendSmtpEmail->setHtmlContent($htmlContent);
            
            if ($textContent) {
                $sendSmtpEmail->setTextContent($textContent);
            }

            // Configurar remitente
            $sendSmtpEmail->setSender([
                'name' => env('BREVO_SENDER_NAME', 'Market Club'),
                'email' => env('BREVO_SENDER_EMAIL', 'noreply@marketclub.com')
            ]);

            // Configurar respuesta
            if ($replyTo) {
                $replyToObj = new SendSmtpEmailReplyTo();
                $replyToObj->setEmail($replyTo['email']);
                $replyToObj->setName($replyTo['name'] ?? 'Market Club');
                $sendSmtpEmail->setReplyTo($replyToObj);
            }

            // Enviar email
            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            
            Log::info('Email sent successfully via Brevo', [
                'message_id' => $result->getMessageId(),
                'to' => $to,
                'subject' => $subject
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Brevo email sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Enviar email de habilitación de mayorista
     */
    public function sendWholesalerActivationEmail(string $email, string $businessName, string $contactName): bool
    {
        $subject = '¡Tu cuenta de mayorista ha sido activada en Market Club!';
        
        $htmlContent = $this->getWholesalerActivationEmailTemplate($businessName, $contactName);
        $textContent = $this->getWholesalerActivationEmailTextTemplate($businessName, $contactName);

        return $this->sendEmail(
            [$email => $contactName],
            $subject,
            $htmlContent,
            $textContent
        );
    }

    /**
     * Template HTML para email de activación de mayorista
     */
    private function getWholesalerActivationEmailTemplate(string $businessName, string $contactName): string
    {
        return '
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
                    <h2>Hola ' . htmlspecialchars($contactName) . ',</h2>
                    <p>¡Excelentes noticias! Tu cuenta de mayorista para <strong>' . htmlspecialchars($businessName) . '</strong> ha sido activada exitosamente.</p>
                    
                    <p>Ahora puedes acceder a todos los beneficios de nuestro programa de mayoristas:</p>
                    <ul>
                        <li>Precios especiales de mayorista</li>
                        <li>Catálogo exclusivo de productos</li>
                        <li>Gestión de pedidos simplificada</li>
                        <li>Soporte prioritario</li>
                    </ul>
                    
                    <p>Para comenzar a realizar pedidos, simplemente inicia sesión en tu cuenta y navega por nuestro catálogo de productos.</p>
                    
                    <div style="text-align: center;">
                        <a href="' . env('APP_URL', 'https://marketclub.com') . '/mayorista/login" class="button">Acceder a mi cuenta</a>
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
    }

    /**
     * Template de texto plano para email de activación de mayorista
     */
    private function getWholesalerActivationEmailTextTemplate(string $businessName, string $contactName): string
    {
        return "
¡Bienvenido a Market Club!

Hola {$contactName},

¡Excelentes noticias! Tu cuenta de mayorista para {$businessName} ha sido activada exitosamente.

Ahora puedes acceder a todos los beneficios de nuestro programa de mayoristas:
- Precios especiales de mayorista
- Catálogo exclusivo de productos
- Gestión de pedidos simplificada
- Soporte prioritario

Para comenzar a realizar pedidos, simplemente inicia sesión en tu cuenta y navega por nuestro catálogo de productos.

Acceder a mi cuenta: " . env('APP_URL', 'https://marketclub.com') . "/mayorista/login

Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.

¡Gracias por ser parte de Market Club!

Market Club - Tu plataforma de confianza para productos de calidad
Este es un email automático, por favor no respondas a este mensaje.
        ";
    }
}
