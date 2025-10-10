<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Método de Pago Actualizado - Market Club</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #f8b739;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Método de Pago Actualizado</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <div class="success-box">
            <h3>Tu método de pago ha sido actualizado exitosamente</h3>
            <p><strong>Fecha:</strong> {{ $updated_at }}</p>
        </div>
        
        <div class="info-box">
            <h3>Detalles de tu Suscripción</h3>
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Nuevo Método de Pago:</strong> {{ $payment_method }}</p>
            <p><strong>Estado:</strong> {{ $subscription->status === 'active' ? 'Activa' : ucfirst($subscription->status) }}</p>
            @if($subscription->next_billing_date)
                <p><strong>Próximo Cobro:</strong> {{ $subscription->next_billing_date->format('d/m/Y') }}</p>
            @endif
        </div>
        
        <h3>¿Qué significa esto?</h3>
        <ul>
            <li>Los futuros cobros se procesarán con este nuevo método de pago</li>
            <li>Tu suscripción continuará sin interrupciones</li>
            <li>@if($subscription->isSuspended())
                Tu suscripción ha sido reactivada automáticamente
                @else
                La renovación automática sigue activa
                @endif
            </li>
        </ul>
        
        <center>
            <a href="{{ config('app.frontend_url') }}/account/subscription" class="button">
                Ver Mi Suscripción
            </a>
        </center>
        
        <p style="margin-top: 30px; background-color: #fff3cd; padding: 15px; border-radius: 5px;">
            <strong>⚠️ Nota de Seguridad:</strong><br>
            Si no realizaste este cambio, por favor contacta con nuestro equipo de soporte inmediatamente.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Market Club. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>

