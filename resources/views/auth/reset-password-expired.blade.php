<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace Expirado - Market Club</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            color: #ef4444;
        }

        h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .message {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .message-text {
            color: #92400e;
            font-size: 14px;
            line-height: 1.5;
        }

        .button {
            background: linear-gradient(135deg, #b58e31 0%, #a07817 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 16px;
            width: 100%;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(181, 142, 49, 0.3);
        }

        .back-link {
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #b58e31;
        }

        @media (max-width: 640px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        
        <h1>Enlace No Válido</h1>
        
        <p class="subtitle">
            Este enlace de restablecimiento de contraseña ya no se puede usar o ha expirado.
        </p>
        
        <div class="message">
            <p class="message-text">
                <strong>¿Por qué sucede esto?</strong><br>
                Los enlaces de restablecimiento de contraseña son de un solo uso por seguridad. Si ya usaste este enlace o han pasado más de 24 horas, necesitarás solicitar uno nuevo.
            </p>
        </div>
        
        <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" class="button">
            Solicitar Nuevo Enlace de Restablecimiento
        </a>
        
        <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" class="back-link">
            ← Volver al inicio
        </a>
    </div>
</body>
</html>
