<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Suspendida - Market Club</title>
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
            background-color: #dc3545;
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
        .alert-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #6c757d;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
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
        <h1>🔒 Suscripción Suspendida</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <div class="alert-box">
            <h3>Tu suscripción ha sido suspendida</h3>
            <p>Después de varios intentos, no pudimos procesar el pago de tu suscripción a <strong>{{ $plan->name }}</strong>.</p>
            <p><strong>Fecha de suspensión:</strong> {{ $suspended_at }}</p>
        </div>
        
        <div class="info-box">
            <h3>¿Qué significa esto?</h3>
            <ul>
                <li>Tu acceso a los beneficios del plan está temporalmente desactivado</li>
                <li>No se realizarán más intentos de cobro automáticos</li>
                <li>Puedes reactivar tu suscripción en cualquier momento</li>
            </ul>
        </div>
        
        <h3>¿Cómo reactivar tu suscripción?</h3>
        <p>Es muy fácil:</p>
        <ol>
            <li>Actualiza tu método de pago</li>
            <li>Reactiva tu suscripción con un clic</li>
            <li>Vuelve a disfrutar de todos los beneficios</li>
        </ol>
        
        <center>
            <a href="{{ config('app.frontend_url') }}/account/subscription/reactivate" class="button">
                Reactivar Suscripción
            </a>
        </center>
        
        <p style="margin-top: 30px;">
            <strong>¿Necesitas ayuda?</strong><br>
            Si tienes problemas con tu método de pago o necesitas asistencia, nuestro equipo está listo para ayudarte.
        </p>
        
        <p style="color: #666; font-size: 14px;">
            También puedes cancelar definitivamente tu suscripción si así lo deseas desde tu panel de cuenta.
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Market Club. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>

