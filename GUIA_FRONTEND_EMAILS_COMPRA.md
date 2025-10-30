# Guía Frontend: Envío de Emails de Compra Exitosa

## 📋 Resumen

El backend ya está configurado para enviar emails automáticamente cuando una compra es exitosa. Esta guía explica qué debe hacer el **frontend** para que se disparen correctamente los emails de confirmación.

---

## 🔄 Flujo Completo de Compra

### 1️⃣ **Crear la Orden**

**Endpoint:** `POST /api/checkout`

```javascript
// Crear orden desde el carrito
const response = await fetch("https://api.marketclub.com/api/checkout", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${userToken}`,
        "Content-Type": "application/json",
        Accept: "application/json",
    },
    body: JSON.stringify({
        shipping_address: {
            name: "Juan Pérez",
            address: "Calle 123 #45-67",
            city: "Bogotá",
            state: "Cundinamarca",
            postal_code: "110111",
            country: "Colombia",
            phone: "+573001234567",
        },
        billing_address: {
            name: "Juan Pérez",
            address: "Calle 123 #45-67",
            city: "Bogotá",
            state: "Cundinamarca",
            postal_code: "110111",
            country: "Colombia",
            phone: "+573001234567",
        },
        notes: "Entregar en horario de oficina", // Opcional
    }),
});

const data = await response.json();
// data.order.id contiene el ID de la orden creada
```

**Respuesta esperada:**

```json
{
  "success": true,
  "message": "Orden creada exitosamente",
  "order": {
    "id": 123,
    "order_number": "ORD-2025-0001",
    "user_id": 1,
    "status": "pending",
    "payment_status": "unpaid",
    "total_amount": 150000,
    "shipping_address": {...},
    "billing_address": {...}
  }
}
```

---

### 2️⃣ **Procesar el Pago**

**Endpoint:** `POST /api/payments/process`

Este es el paso **crítico** donde se envía el email de compra exitosa.

```javascript
// Procesar pago con Wompi
const paymentResponse = await fetch(
    "https://api.marketclub.com/api/payments/process",
    {
        method: "POST",
        headers: {
            Authorization: `Bearer ${userToken}`,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            order_id: data.order.id,
            payment_method_type: "CARD", // o "PSE", "NEQUI"
            payment_token: "tok_test_xxxxxxxx", // Token de Wompi
            installments: 1,
        }),
    }
);

const paymentData = await paymentResponse.json();
```

**Respuesta cuando el pago es APROBADO:**

```json
{
    "success": true,
    "message": "Pago procesado exitosamente",
    "data": {
        "transaction_id": "12345678-1234-1234-1234-123456789012",
        "status": "APPROVED",
        "payment_url": null,
        "order": {
            "id": 123,
            "status": "processing",
            "payment_status": "paid",
            "payment_reference": "12345678-1234-1234-1234-123456789012"
        }
    }
}
```

### ✅ **En este momento se envían AUTOMÁTICAMENTE:**

1. ✉️ Email de confirmación de orden (`sendOrderConfirmation`)
2. ✉️ Email de confirmación de pago (`sendPaymentConfirmation`)

---

### 3️⃣ **Verificar el Pago (Alternativa)**

**Endpoint:** `POST /api/payments/verify`

Si el pago no se procesa inmediatamente, puedes verificar su estado:

```javascript
// Verificar estado del pago
const verifyResponse = await fetch(
    "https://api.marketclub.com/api/payments/verify",
    {
        method: "POST",
        headers: {
            Authorization: `Bearer ${userToken}`,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            transaction_id: "12345678-1234-1234-1234-123456789012",
        }),
    }
);

const verifyData = await verifyResponse.json();
```

**El email también se envía cuando:**

-   `verifyPayment` detecta que el pago fue aprobado
-   El webhook de Wompi notifica un pago exitoso

---

## 🎯 ¿Qué debe hacer el Frontend?

### ✅ **LO QUE SÍ DEBE HACER:**

1. **Llamar a `/api/checkout`** para crear la orden
2. **Llamar a `/api/payments/process`** con los datos correctos:
    - `order_id` válido
    - `payment_method_type` correcto
    - `payment_token` si es tarjeta
3. **Manejar la respuesta** del pago:
    - Si `status === "APPROVED"` → El email ya fue enviado
    - Si `status === "PENDING"` → Esperar confirmación por webhook
    - Si `status === "DECLINED"` → El usuario recibe email de pago fallido
4. **Redirigir al usuario** a la página de confirmación

### ❌ **LO QUE NO DEBE HACER:**

-   ❌ **NO** intentar enviar el email desde el frontend
-   ❌ **NO** llamar a endpoints adicionales de email
-   ❌ **NO** preocuparse por el envío del email (es automático)

---

## 📊 Estados del Pago y Emails

| Estado Wompi | Estado Orden          | Email Enviado                                   |
| ------------ | --------------------- | ----------------------------------------------- |
| `APPROVED`   | `processing` / `paid` | ✅ Confirmación de orden + confirmación de pago |
| `PENDING`    | `pending`             | ⏳ Ninguno (espera confirmación)                |
| `DECLINED`   | `pending` / `failed`  | ❌ Email de pago fallido                        |

---

## 🔍 Debugging: ¿Por qué no llega el email?

### 1. **Verificar configuración de Brevo en el backend**

El backend necesita estas variables configuradas en `.env`:

```env
BREVO_API_KEY=tu_api_key_aqui
BREVO_SENDER_NAME="Market Club"
BREVO_SENDER_EMAIL="noreply@marketclub.com"
APP_URL=https://marketclub.com
```

### 2. **Verificar logs del backend**

Revisar el archivo `storage/logs/laravel.log` para ver si hay errores:

```bash
# Buscar logs de email
tail -f storage/logs/laravel.log | grep -i "email\|brevo"
```

**Logs esperados cuando funciona:**

```
[2025-10-30 10:00:00] local.INFO: Order confirmation email sent for order 123 with status: APPROVED
[2025-10-30 10:00:00] local.INFO: Email sent successfully via Brevo {"message_id":"..."}
```

**Logs de error si no funciona:**

```
[2025-10-30 10:00:00] local.ERROR: Failed to send order confirmation email for order 123: ...
[2025-10-30 10:00:00] local.ERROR: Brevo email sending failed: ...
```

### 3. **Verificar que el pago llegó como APPROVED**

```javascript
// El frontend debe verificar que el pago fue exitoso
if (paymentData.success && paymentData.data.status === "APPROVED") {
    console.log("✅ Pago exitoso - Email enviado automáticamente");
    // Redirigir a página de confirmación
    window.location.href = `/order-confirmation/${orderId}`;
}
```

### 4. **Probar con el endpoint de verificación**

```javascript
// Si tienes dudas, verifica el estado del pago
const transaction_id = paymentData.data.transaction_id;

const verify = await fetch("/api/payments/verify", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    },
    body: JSON.stringify({ transaction_id }),
});
```

---

## 🛠️ Código de Ejemplo Frontend Completo

```javascript
// Flujo completo de compra
async function completePurchase(shippingData, billingData, paymentToken) {
    try {
        // 1. Crear orden
        const checkoutResponse = await fetch("/api/checkout", {
            method: "POST",
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                shipping_address: shippingData,
                billing_address: billingData,
            }),
        });

        const checkoutData = await checkoutResponse.json();

        if (!checkoutData.success) {
            throw new Error(checkoutData.message);
        }

        const orderId = checkoutData.order.id;
        console.log("✅ Orden creada:", orderId);

        // 2. Procesar pago
        const paymentResponse = await fetch("/api/payments/process", {
            method: "POST",
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                order_id: orderId,
                payment_method_type: "CARD",
                payment_token: paymentToken,
                installments: 1,
            }),
        });

        const paymentData = await paymentResponse.json();

        if (!paymentData.success) {
            throw new Error(paymentData.message);
        }

        // 3. Verificar estado del pago
        if (paymentData.data.status === "APPROVED") {
            console.log("✅ Pago aprobado - Email enviado automáticamente");

            // Redirigir a página de confirmación
            window.location.href = `/order-confirmation/${orderId}`;
        } else if (paymentData.data.status === "PENDING") {
            console.log("⏳ Pago pendiente - Esperando confirmación");

            // Mostrar mensaje de espera o redirigir
            window.location.href = `/payment-pending/${orderId}`;
        } else if (paymentData.data.status === "DECLINED") {
            console.log("❌ Pago rechazado");

            // Mostrar error al usuario
            alert(
                "Lo sentimos, el pago fue rechazado. Por favor intenta con otro método de pago."
            );
        }
    } catch (error) {
        console.error("Error en el proceso de compra:", error);
        alert(
            "Ocurrió un error al procesar tu compra. Por favor intenta nuevamente."
        );
    }
}
```

---

## 📧 Templates de Email Disponibles

El backend ya tiene configurados estos templates:

1. **`order-confirmation.blade.php`** - Email de confirmación de orden

    - Se envía cuando el pago es APPROVED
    - Incluye detalles de la orden, productos, totales

2. **`payment-confirmation.blade.php`** - Email de confirmación de pago

    - Se envía junto con la confirmación de orden
    - Confirma que el pago fue procesado exitosamente

3. **`payment-failed.blade.php`** - Email de pago fallido
    - Se envía cuando el pago es DECLINED
    - Ofrece alternativas al usuario

---

## 🔐 Headers Requeridos

**Para usuarios autenticados:**

```javascript
headers: {
  'Authorization': `Bearer ${userToken}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

**Para usuarios invitados (checkout):**

```javascript
headers: {
  'X-Session-Id': sessionId, // Generar en frontend
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

---

## ✅ Checklist Final

-   [ ] El frontend llama a `/api/checkout` correctamente
-   [ ] El frontend llama a `/api/payments/process` con `order_id` válido
-   [ ] El backend tiene configurado `BREVO_API_KEY`
-   [ ] El backend tiene configurado `BREVO_SENDER_EMAIL`
-   [ ] El email del remitente está verificado en Brevo
-   [ ] Los logs muestran "Order confirmation email sent"
-   [ ] El usuario tiene un email válido en su cuenta
-   [ ] El pago llega con estado `APPROVED` en la respuesta

---

## 🆘 Soporte

Si después de seguir esta guía los emails no se envían:

1. **Revisar logs del backend** en `storage/logs/laravel.log`
2. **Verificar API Key de Brevo** en el dashboard de Brevo
3. **Verificar email del remitente** está verificado en Brevo
4. **Contactar al equipo de backend** con los logs de error
