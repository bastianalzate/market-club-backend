# Configuración de Wompi para Market Club

## 🔧 Variables de Entorno Requeridas

Agrega estas variables a tu archivo `.env`:

```env
# Configuración de Wompi
WOMPI_PUBLIC_KEY=pub_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
WOMPI_PRIVATE_KEY=prv_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
WOMPI_PRODUCTION=false

# URL del frontend para redirecciones
FRONTEND_URL=http://localhost:3000
```

## 📋 Pasos para Configurar Wompi

### 1. Crear Cuenta en Wompi

-   Ve a [Wompi](https://wompi.com/es/co/)
-   Regístrate como comerciante
-   Completa la verificación de documentos

### 2. Obtener Credenciales

-   Accede al panel de desarrolladores
-   Copia tu **Public Key** y **Private Key**
-   Para pruebas usa las credenciales de **Sandbox**
-   Para producción usa las credenciales de **Producción**

### 3. Configurar Webhook

-   En el panel de Wompi, configura el webhook:
-   URL: `https://tu-dominio.com/api/payments/webhook`
-   Eventos: `transaction.updated`

## 🚀 Endpoints de la API

### Crear Token de Pago

```http
POST /api/payments/token
Content-Type: application/json

{
    "number": "4242424242424242",
    "cvc": "123",
    "exp_month": "12",
    "exp_year": "2025",
    "card_holder": "Juan Pérez"
}
```

### Procesar Pago

```http
POST /api/payments/process
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 1,
    "payment_method_type": "CARD",
    "payment_token": "tok_test_xxxxxxxx",
    "installments": 1
}
```

### Verificar Pago

```http
POST /api/payments/verify
Authorization: Bearer {token}
Content-Type: application/json

{
    "transaction_id": "12345678-1234-1234-1234-123456789012"
}
```

### Obtener Métodos de Pago

```http
GET /api/payments/methods
```

## 💳 Métodos de Pago Soportados

-   **CARD**: Tarjetas de crédito/débito
-   **PSE**: Pago Seguro en Línea
-   **NEQUI**: Nequi
-   **BANCOLOMBIA_TRANSFER**: Transferencia Bancolombia

## 🔄 Flujo de Pago

1. **Frontend** crea token de pago con datos de tarjeta
2. **Frontend** envía orden con token a `/api/payments/process`
3. **Backend** procesa pago con Wompi
4. **Wompi** envía webhook con resultado
5. **Backend** actualiza estado de orden
6. **Frontend** redirige según resultado

## 🧪 Datos de Prueba

### Tarjetas de Prueba (Sandbox)

-   **Aprobada**: 4242424242424242
-   **Rechazada**: 4000000000000002
-   **CVC**: 123
-   **Fecha**: Cualquier fecha futura

### Montos de Prueba

-   **Aprobado**: Cualquier monto
-   **Rechazado**: $1,000,000 COP

## 🔒 Seguridad

-   Las credenciales privadas nunca se exponen al frontend
-   Los tokens de pago son de un solo uso
-   Los webhooks se verifican con firma HMAC
-   Todas las transacciones se registran en la base de datos

## 📊 Estados de Transacción

-   **PENDING**: Pago pendiente
-   **APPROVED**: Pago aprobado
-   **DECLINED**: Pago rechazado
-   **VOIDED**: Pago anulado

## 🚨 Troubleshooting

### Error: "Invalid signature"

-   Verifica que la firma del webhook sea correcta
-   Asegúrate de usar la private key correcta

### Error: "Transaction not found"

-   Verifica que el ID de transacción sea correcto
-   Revisa que la transacción exista en Wompi

### Error: "Payment method not supported"

-   Verifica que el método de pago esté habilitado en tu cuenta Wompi
-   Revisa la configuración de métodos de pago
