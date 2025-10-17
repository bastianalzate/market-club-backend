<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Renovada - Market Club</title>
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
            background-color: #f8b739;
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
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #f8b739;
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
        <h1>✅ Suscripción Renovada</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>¡Buenas noticias! Tu suscripción a <strong>{{ $plan->name }}</strong> ha sido renovada exitosamente.</p>
        
        <div class="info-box">
            <h3>Detalles de la Renovación</h3>
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Monto:</strong> ${{ number_format($amount, 0, ',', '.') }} COP</p>
            <p><strong>Período:</strong> {{ $subscription->starts_at->format('d/m/Y') }} - {{ $subscription->ends_at->format('d/m/Y') }}</p>
            <p><strong>Próxima Facturación:</strong> {{ $next_billing_date }}</p>
            <p><strong>ID de Transacción:</strong> {{ $transaction->transaction_id }}</p>
        </div>
        
        <p>Tu suscripción seguirá activa y disfrutarás de todos los beneficios de tu plan.</p>
        
        <center>
            <a href="{{ config('app.frontend_url') }}/account/subscription" class="button">
                Ver Mi Suscripción
            </a>
        </center>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Market Club. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>

