<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Market Club</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #B58E31 0%, #D4AF37 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #B58E31 0%, #D4AF37 100%);
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            color: #856404;
        }
        .warning h3 {
            margin-top: 0;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .link {
            color: #B58E31;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍺 Market Club</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $user->name }}!
            </div>
            
            <div class="message">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta en Market Club.
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">
                    Restablecer Mi Contraseña
                </a>
            </div>
            
            <div class="warning">
                <h3>⚠️ Información Importante</h3>
                <ul>
                    <li>Este enlace es válido por <strong>24 horas</strong></li>
                    <li>Solo puede ser usado <strong>una vez</strong></li>
                    <li>Si no solicitaste este cambio, puedes ignorar este email</li>
                    <li>Tu contraseña actual seguirá funcionando hasta que la cambies</li>
                </ul>
            </div>
            
            <div class="message">
                Si el botón no funciona, copia y pega este enlace en tu navegador:
                <br>
                <a href="{{ $resetUrl }}" class="link">{{ $resetUrl }}</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Market Club</strong></p>
            <p>Tu tienda de cervezas artesanales favorita</p>
            <p>Este email fue enviado automáticamente, por favor no respondas.</p>
        </div>
    </div>
</body>
</html>
