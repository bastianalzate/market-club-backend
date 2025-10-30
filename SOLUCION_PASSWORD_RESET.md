# 🔧 Solución: Password Reset Redirige al Frontend

## ❌ **PROBLEMA**

El email de restablecimiento de contraseña está enviando al usuario a:

```
https://market-club-frontend.vercel.app/reset-password?token=...
```

Cuando debería enviarlo a:

```
https://admin-dev.marketclub.com.co/reset-password?token=...
```

O en local:

```
http://localhost:8000/reset-password?token=...
```

---

## 🎯 **CAUSA**

En el archivo `.env`, la variable `APP_URL` está configurada con la URL del **frontend** en lugar del **backend**.

---

## ✅ **SOLUCIÓN IMPLEMENTADA**

### **Detección Automática**

El backend ahora detecta automáticamente si `APP_URL` apunta al frontend y usa la URL de la petición actual (que siempre será el backend).

**Código implementado en `PasswordResetController.php`:**
```php
// Generar URL de reset apuntando siempre al backend
$backendUrl = config('app.url');

// Si app.url apunta al frontend, usar la URL actual de la petición
if (str_contains($backendUrl, 'vercel.app') || str_contains($backendUrl, 'localhost:3000')) {
    $backendUrl = request()->getSchemeAndHttpHost();
}

$resetUrl = $backendUrl . '/reset-password?token=' . $passwordReset->token;
```

### **Resultado:**

✅ **En Local:**
- Petición desde: `http://localhost:3000` (frontend)
- Email genera: `http://localhost:8000/reset-password?token=...` (backend)

✅ **En Producción:**
- Petición desde: `https://market-club-frontend.vercel.app` (frontend)
- Email genera: `https://admin-dev.marketclub.com.co/reset-password?token=...` (backend)

---

## 🚀 **DESPLEGAR EN PRODUCCIÓN**

```bash
# Conectarse al servidor
ssh marketclub-admin-dev@srv829831.hstgr.cloud

# Ir al directorio del proyecto
cd htdocs/admin-dev.marketclub.com.co

# Subir los cambios
git pull origin main

# Limpiar cache
php artisan config:clear
php artisan cache:clear
```

**¡Listo!** El código ya tiene la lógica de detección automática, no necesitas cambiar el `.env`.

---

## 🧪 **PROBAR**

1. Desde el frontend, solicitar reset de contraseña
2. Revisar el email que llega
3. El enlace debería ser: `https://admin-dev.marketclub.com.co/reset-password?token=...`
4. Al hacer clic, debería abrir la página del backend (Laravel Blade)

---

## 📋 **VARIABLES DE ENTORNO CORRECTAS**

### **Backend (.env)**

```env
APP_URL=https://admin-dev.marketclub.com.co
FRONTEND_URL=https://market-club-frontend.vercel.app
```

### **Frontend (.env)**

```env
REACT_APP_API_URL=https://admin-dev.marketclub.com.co/api
```

---

## 🔍 **VERIFICAR LOGS**

Después de solicitar el reset, verificar los logs:

```bash
tail -f storage/logs/laravel.log | grep -i "reset"
```

Deberías ver:

```
[2025-10-30 XX:XX:XX] local.INFO: Generated reset URL: https://admin-dev.marketclub.com.co/reset-password?token=...
[2025-10-30 XX:XX:XX] local.INFO: Password reset requested for user XX with email xxx@xxx.com
```

---

## 📝 **RESUMEN DE LA DIFERENCIA**

| Variable       | Propósito                     | Valor Correcto                            |
| -------------- | ----------------------------- | ----------------------------------------- |
| `APP_URL`      | URL del **backend** (Laravel) | `https://admin-dev.marketclub.com.co`     |
| `FRONTEND_URL` | URL del **frontend** (React)  | `https://market-club-frontend.vercel.app` |

---

## ✅ **DESPUÉS DE APLICAR LA SOLUCIÓN**

El flujo correcto será:

1. Usuario solicita reset desde frontend
2. Backend genera URL: `https://admin-dev.marketclub.com.co/reset-password?token=...`
3. Email enviado con el enlace correcto
4. Usuario hace clic → se abre página del backend
5. Usuario cambia contraseña en el backend
6. Backend confirma éxito
7. Usuario puede volver al frontend manualmente

---

¡Con esto debería funcionar correctamente! 🎉
