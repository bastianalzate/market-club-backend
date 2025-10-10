<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema con el Pago - Market Club</title>
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
            background-color: #ff6b6b;
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
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ff6b6b;
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
        <h1>⚠️ Problema con tu Pago</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>No pudimos procesar el pago de tu suscripción a <strong>{{ $plan->name }}</strong>.</p>
        
        <div class="info-box">
            <h3>Detalles del Problema</h3>
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Método de Pago:</strong> {{ $payment_method }}</p>
            <p><strong>Error:</strong> {{ $error_message }}</p>
        </div>
        
        <div class="warning-box">
            <h3>¿Qué significa esto?</h3>
            <p><strong>Reintentos Automáticos:</strong> Intentaremos procesar el pago {{ $retries_left }} vez(veces) más en los próximos días.</p>
            <p><strong>Intento actual:</strong> {{ $retry_count }} de 4</p>
            
            @if($retries_left > 0)
                <p>Tu suscripción sigue activa. Volveremos a intentar el cobro pronto.</p>
            @else
                <p style="color: #d9534f;"><strong>Advertencia:</strong> Este fue el último intento. Por favor, actualiza tu método de pago para evitar la suspensión.</p>
            @endif
        </div>
        
        <h3>¿Qué puedes hacer?</h3>
        <ul>
            <li>Verificar que tu tarjeta tenga fondos suficientes</li>
            <li>Actualizar tu método de pago</li>
            <li>Contactar con tu banco si es necesario</li>
        </ul>
        
        <center>
            <a href="{{ config('app.frontend_url') }}/account/subscription/payment-method" class="button">
                Actualizar Método de Pago
            </a>
        </center>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Si necesitas ayuda, estamos aquí para asistirte. Contáctanos en cualquier momento.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Market Club. Todos los derechos reservados.</p>
    </div>
</body>
</html>

