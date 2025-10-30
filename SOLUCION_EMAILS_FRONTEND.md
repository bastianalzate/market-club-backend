# ✅ SOLUCIÓN: Envío de Emails de Compra

## 🔴 **PROBLEMA IDENTIFICADO**

El frontend está llamando a `/api/payments/process` DESPUÉS de que Wompi ya procesó el pago. Esto está INCORRECTO porque:

1. `/api/payments/process` está diseñado para **INICIAR** un pago, no para confirmarlo
2. Ese endpoint intenta crear una NUEVA transacción en Wompi
3. Puede causar transacciones duplicadas o errores

---

## ✅ **SOLUCIONES DISPONIBLES**

### **Solución 1: Usar el Webhook de Wompi (RECOMENDADO)**

El webhook YA está funcionando y enviando emails. El frontend solo necesita:

1. **NO llamar a `/api/payments/process`**
2. Esperar unos segundos después del callback de Wompi
3. Verificar el estado de la orden con polling

**Código para el frontend:**

```typescript
// En PaymentStep.tsx, después de que Wompi confirma el pago:

const handleWompiCallback = async (transaction: any) => {
    if (transaction.status === "APPROVED") {
        console.log("✅ Payment approved by Wompi");
        console.log("⏳ Waiting for webhook to process...");

        // Esperar 3 segundos para que el webhook procese
        await new Promise((resolve) => setTimeout(resolve, 3000));

        // Hacer polling para verificar el estado
        const maxAttempts = 5;
        let attempt = 0;

        while (attempt < maxAttempts) {
            try {
                const orderStatus = await checkOrderStatus(orderId);

                if (orderStatus.payment_status === "paid") {
                    console.log(
                        "✅ Order confirmed and emails sent by webhook"
                    );
                    navigate("/checkout/success");
                    return;
                }

                // Esperar 2 segundos antes del próximo intento
                await new Promise((resolve) => setTimeout(resolve, 2000));
                attempt++;
            } catch (error) {
                console.error("Error checking order status:", error);
                attempt++;
            }
        }

        // Si después de 5 intentos no está confirmado, usar el endpoint de confirmación manual
        console.warn(
            "⚠️ Webhook didnt confirm payment, using manual confirmation..."
        );
        await confirmPaymentManually(orderId, transaction.id);
    }
};

const checkOrderStatus = async (orderId: number) => {
    const response = await fetch(`/api/user/orders/${orderId}`, {
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });
    return response.json();
};

const confirmPaymentManually = async (
    orderId: number,
    transactionId: string
) => {
    const response = await fetch("/api/payments/confirm", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            order_id: orderId,
            transaction_id: transactionId,
        }),
    });

    const result = await response.json();

    if (result.success && result.data.payment_status === "paid") {
        console.log("✅ Payment confirmed manually and emails sent");
        navigate("/checkout/success");
    }
};
```

---

### **Solución 2: NUEVO Endpoint `/api/payments/confirm`**

He creado un endpoint específico para que el frontend confirme pagos ya procesados por Wompi.

**Endpoint:** `POST /api/payments/confirm`

**Request:**

```json
{
    "order_id": 123,
    "transaction_id": "12345-6789-ABCD" // ID de transacción de Wompi
}
```

**Response:**

```json
{
  "success": true,
  "message": "Pago confirmado exitosamente",
  "data": {
    "order": {
      "id": 123,
      "order_number": "ORD-123",
      "payment_status": "paid",
      "status": "processing"
    },
    "payment_status": "paid",
    "transaction": {...}
  }
}
```

**¿Qué hace este endpoint?**

1. ✅ Verifica el pago con Wompi
2. ✅ Actualiza el estado de la orden
3. ✅ **ENVÍA LOS EMAILS** si el pago está aprobado
4. ✅ Retorna el estado actualizado

**Cuándo usar este endpoint:**

-   Como respaldo si el webhook no funcionó
-   Después de esperar unos segundos y el polling no confirma el pago

---

## 🚀 **IMPLEMENTACIÓN RECOMENDADA (HÍBRIDA)**

Esta es la mejor solución que combina ambas:

```typescript
// src/components/checkout/PaymentStep.tsx

const handleWompiCallback = async (transaction: any) => {
    if (transaction.status !== "APPROVED") {
        console.error("❌ Payment not approved:", transaction.status);
        return;
    }

    console.log("✅ Payment approved by Wompi");
    console.log("Transaction ID:", transaction.id);

    try {
        // ESTRATEGIA 1: Esperar al webhook (más confiable)
        console.log("⏳ Waiting for webhook to process payment...");
        await new Promise((resolve) => setTimeout(resolve, 3000));

        // Intentar verificar con polling (5 intentos, 2 segundos cada uno)
        for (let attempt = 1; attempt <= 5; attempt++) {
            console.log(`🔍 Verification attempt ${attempt}/5...`);

            try {
                const orderStatus = await checkOrderStatus(orderId);

                if (orderStatus.payment_status === "paid") {
                    console.log("✅ Webhook confirmed payment successfully!");
                    console.log("📧 Emails sent by webhook");
                    navigate("/checkout/success");
                    return;
                }
            } catch (error) {
                console.error(`Attempt ${attempt} failed:`, error);
            }

            // Esperar antes del próximo intento
            if (attempt < 5) {
                await new Promise((resolve) => setTimeout(resolve, 2000));
            }
        }

        // ESTRATEGIA 2: Si el webhook no confirmó, usar confirmación manual
        console.warn("⚠️ Webhook didnt confirm after 5 attempts");
        console.log("🔄 Using manual confirmation endpoint...");

        const confirmResponse = await fetch("/api/payments/confirm", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({
                order_id: orderId,
                transaction_id: transaction.id,
            }),
        });

        const confirmResult = await confirmResponse.json();

        if (
            confirmResult.success &&
            confirmResult.data.payment_status === "paid"
        ) {
            console.log("✅ Payment confirmed manually!");
            console.log("📧 Emails sent via manual confirmation");
            navigate("/checkout/success");
        } else {
            throw new Error("Manual confirmation failed");
        }
    } catch (error) {
        console.error("❌ Error processing payment:", error);
        // Mostrar error al usuario
        alert(
            "Hubo un problema al confirmar tu pago. Por favor contacta a soporte."
        );
    }
};
```

---

## 📊 **FLUJO CORRECTO**

```
1. Usuario paga en Widget de Wompi
   ↓
2. Wompi confirma: transaction.status = "APPROVED"
   ↓
3. Wompi envía WEBHOOK al backend ← (Aquí se envían los emails)
   ↓
4. Frontend espera 3 segundos
   ↓
5. Frontend hace POLLING (5 intentos)
   ├─ Si payment_status = "paid" → ✅ Webhook funcionó
   │  └─ Redirigir a /checkout/success
   │
   └─ Si después de 5 intentos NO está "paid"
      ↓
      6. Frontend llama a /api/payments/confirm (respaldo)
         └─ Backend verifica con Wompi y envía emails
         └─ ✅ Redirigir a /checkout/success
```

---

## 🗑️ **QUÉ ELIMINAR DEL FRONTEND**

**ELIMINAR esta llamada:**

```typescript
// ❌ NO HACER ESTO:
await fetch("/api/payments/process", {
    method: "POST",
    body: JSON.stringify({
        order_id: orderId,
        payment_method_type: "CARD",
        payment_token: transaction.id, // ← Esto es incorrecto
    }),
});
```

**Motivo:**

-   Ese endpoint intenta CREAR una nueva transacción
-   Ya tienes una transacción aprobada de Wompi
-   Puede causar duplicados o errores

---

## ✅ **CHECKLIST DE IMPLEMENTACIÓN**

-   [ ] Eliminar llamada a `/api/payments/process` después del widget
-   [ ] Implementar espera de 3 segundos después del callback
-   [ ] Implementar polling con `checkOrderStatus()` (5 intentos)
-   [ ] Implementar confirmación manual como respaldo con `/api/payments/confirm`
-   [ ] Actualizar logs en la consola para debugging
-   [ ] Desplegar cambios en el backend (nuevo endpoint)
-   [ ] Probar flujo completo en ambiente de desarrollo
-   [ ] Probar flujo completo en producción

---

## 🔍 **DEBUGGING**

### Logs esperados en la consola del navegador:

```
✅ Payment approved by Wompi
Transaction ID: 12345-6789-ABCD
⏳ Waiting for webhook to process payment...
🔍 Verification attempt 1/5...
🔍 Verification attempt 2/5...
✅ Webhook confirmed payment successfully!
📧 Emails sent by webhook
```

### Si el webhook falla:

```
✅ Payment approved by Wompi
Transaction ID: 12345-6789-ABCD
⏳ Waiting for webhook to process payment...
🔍 Verification attempt 1/5...
🔍 Verification attempt 2/5...
🔍 Verification attempt 3/5...
🔍 Verification attempt 4/5...
🔍 Verification attempt 5/5...
⚠️ Webhook didnt confirm after 5 attempts
🔄 Using manual confirmation endpoint...
✅ Payment confirmed manually!
📧 Emails sent via manual confirmation
```

---

## 📧 **RESULTADO ESPERADO**

Cuando el pago es exitoso, el usuario debe recibir **2 emails**:

1. **Email de Confirmación de Orden**

    - Asunto: "Confirmación de Orden #ORD-123"
    - Detalles de los productos
    - Dirección de envío
    - Total pagado

2. **Email de Confirmación de Pago**
    - Asunto: "Pago Confirmado - Orden #ORD-123"
    - Confirmación del método de pago
    - Estado del pago

---

## 🆘 **SI AÚN NO FUNCIONAN LOS EMAILS**

1. **Verificar que el backend tenga los cambios desplegados:**

    ```bash
    grep -n "confirmPayment" /ruta/al/proyecto/app/Http/Controllers/Api/PaymentController.php
    ```

2. **Verificar que la ruta esté registrada:**

    ```bash
    cd /ruta/al/proyecto
    php artisan route:list | grep payments/confirm
    ```

3. **Verificar configuración de Brevo:**

    ```bash
    php artisan tinker --execute="echo env('BREVO_API_KEY') ? 'OK' : 'MISSING';"
    ```

4. **Monitorear logs durante una compra:**
    ```bash
    tail -f storage/logs/laravel.log | grep -i "email\|confirmation"
    ```

---

**Fecha:** 30 de Octubre, 2025  
**Versión:** 2.0 (con endpoint `/api/payments/confirm`)
