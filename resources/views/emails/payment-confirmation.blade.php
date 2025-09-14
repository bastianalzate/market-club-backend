<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Confirmado - Market Club</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #059669;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9fafb;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .order-details {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Market Club</h1>
            <h2>Pago Confirmado</h2>
        </div>

        <div class="content">
            <p>Hola {{ $user->name }},</p>

            <div class="success">
                <h3>✅ ¡Pago Exitoso!</h3>
                <p>Tu pago ha sido procesado correctamente y tu orden está siendo preparada.</p>
            </div>

            <div class="order-details">
                <h3>Detalles del Pago</h3>
                <p><strong>Número de Orden:</strong> #{{ $order->order_number }}</p>
                <p><strong>Método de Pago:</strong> {{ $order->payment_method }}</p>
                <p><strong>Estado del Pago:</strong> {{ ucfirst($order->payment_status) }}</p>
                <p><strong>Monto Pagado:</strong> ${{ number_format($order->total_amount, 0, ',', '.') }}</p>
                <p><strong>Fecha del Pago:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            <p>Tu orden está ahora en proceso y será enviada pronto. Te notificaremos cuando esté en camino.</p>

            <p>¡Gracias por tu compra en Market Club!</p>
        </div>

        <div class="footer">
            <p>Market Club - Tu tienda de cervezas artesanales</p>
            <p>Este es un email automático, por favor no responder.</p>
        </div>
    </div>
</body>

</html>
