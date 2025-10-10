<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Próximo Cobro - Market Club</title>
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
            background-color: #17a2b8;
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
        .info-box {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
            border-radius: 5px;
        }
        .highlight {
            background-color: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #f8b739;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
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
        <h1>🔔 Recordatorio de Renovación</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>Este es un recordatorio amistoso de que tu suscripción a <strong>{{ $plan->name }}</strong> se renovará pronto.</p>
        
        <div class="highlight">
            <h2 style="margin: 0; color: #17a2b8;">
                Tu próximo cobro es en {{ $days_until_billing }} días
            </h2>
            <p style="margin: 10px 0 0 0; font-size: 18px;">
                <strong>{{ $billing_date }}</strong>
            </p>
        </div>
        
        <div class="info-box">
            <h3>Detalles del Cobro</h3>
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Monto:</strong> ${{ number_format($amount, 0, ',', '.') }} COP</p>
            <p><strong>Método de Pago:</strong> {{ $payment_method }}</p>
            <p><strong>Fecha de Cobro:</strong> {{ $billing_date }}</p>
        </div>
        
        <h3>¿Qué debes hacer?</h3>
        <p>✅ <strong>Nada!</strong> El cobro se procesará automáticamente con tu método de pago guardado.</p>
        
        <p>Sin embargo, asegúrate de que:</p>
        <ul>
            <li>Tu tarjeta tenga fondos suficientes</li>
            <li>Tu método de pago esté actualizado</li>
            <li>No haya restricciones en tu cuenta bancaria</li>
        </ul>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.frontend_url') }}/account/subscription" class="button">
                Ver Detalles
            </a>
            <a href="{{ config('app.frontend_url') }}/account/subscription/payment-method" class="button" style="background-color: #6c757d;">
                Actualizar Método de Pago
            </a>
        </div>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            <strong>¿No quieres renovar?</strong><br>
            Puedes desactivar la renovación automática o cancelar tu suscripción desde tu panel de cuenta.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Market Club. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>

