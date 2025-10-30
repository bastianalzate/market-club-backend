<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmación de Compra - Market Club</title>
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
            background: #B48C2B;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 28px;
            width: 28px;
            border-radius: 4px;
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
            text-align: center;
        }

        .order-details {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .total {
            font-weight: bold;
            font-size: 18px;
            color: #1f2937;
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
            <div class="logo" style="justify-content:center;">
                <img src="{{ config('app.frontend_url') }}/logo.png" alt="Market Club">
                <h1 style="margin:0;">Market Club</h1>
            </div>
            <h2>¡Compra Exitosa!</h2>
        </div>

        <div class="content">
            <p>Hola {{ $user->name ?? 'Cliente' }},</p>

            <div class="success">
                <h3>✅ ¡Pago Confirmado!</h3>
                <p>Tu pago ha sido procesado correctamente y tu orden está siendo preparada.</p>
            </div>

            <div class="order-details">
                <h3>Detalles de tu Orden</h3>
                <p style="margin: 0 0 10px 0; color:#374151;">Tiempo de envío estimado: 1 a 3 días hábiles.</p>
                <p><strong>Número de Orden:</strong> #{{ $order->order_number }}</p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Método de Pago:</strong> {{ $order->payment_method ?? 'N/A' }}</p>

                <h4>Productos:</h4>
                @foreach ($items as $item)
                    <div class="item">
                        <span>{{ $item->product->name ?? 'Producto' }} (x{{ $item->quantity }})</span>
                        <span>${{ number_format($item->total_price, 0, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="item">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="item">
                    <span>Envío:</span>
                    <span>${{ number_format(12000, 0, ',', '.') }}</span>
                </div>
                <div class="item total">
                    <span>Total Pagado:</span>
                    <span>${{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="order-details">
                <h3>Dirección de Envío</h3>
                <p><strong>{{ $order->shipping_address['name'] }}</strong></p>
                <p>{{ $order->shipping_address['address'] }}</p>
                <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }}</p>
                <p>{{ $order->shipping_address['postal_code'] }}, {{ $order->shipping_address['country'] }}</p>
                <p>Tel: {{ $order->shipping_address['phone'] }}</p>
            </div>

            <p>Tu orden será enviada pronto. Te notificaremos cuando esté en camino.</p>

            <p>¡Gracias por elegir Market Club!</p>
        </div>

        <div class="footer">
            <p>Market Club - "Somos el parche hecho pa' compartir, relajarse y disfrutar sabores del mundo, pero con el
                corazón de Medellín."</p>
            <p>Este es un email automático, por favor no responder.</p>
        </div>
    </div>
</body>

</html>
