# ⚙️ Configuración de Variables de Entorno (.env)

## 📋 **Variables Importantes**

### **Backend (.env)**

```env
# ============================================
# URLs DE LA APLICACIÓN
# ============================================

# URL del backend (Laravel)
APP_URL=http://localhost:8000
# En producción: APP_URL=https://admin-dev.marketclub.com.co

# URL del frontend (React) - Usada en enlaces de emails y redirecciones
FRONTEND_URL=http://localhost:3000
# En producción: FRONTEND_URL=https://market-club-frontend.vercel.app

# URL para reset de contraseña (debe apuntar al backend)
RESET_PASSWORD_URL=http://localhost:8000
# En producción: RESET_PASSWORD_URL=https://admin-dev.marketclub.com.co
```

---

## 🎯 **Propósito de Cada Variable**

| Variable             | Uso                                             | Ejemplo Local           | Ejemplo Producción                        |
| -------------------- | ----------------------------------------------- | ----------------------- | ----------------------------------------- |
| `APP_URL`            | URL base del backend (puede ser cualquier cosa) | `http://localhost:8000` | `https://admin-dev.marketclub.com.co`     |
| `FRONTEND_URL`       | URL del frontend en enlaces de emails           | `http://localhost:3000` | `https://market-club-frontend.vercel.app` |
| `RESET_PASSWORD_URL` | URL para enlaces de reset (DEBE ser backend)    | `http://localhost:8000` | `https://admin-dev.marketclub.com.co`     |

---

## 🔧 **Configuración Local**

Agrega esto a tu `.env` local:

```env
APP_NAME=MarketClub
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

FRONTEND_URL=http://localhost:3000
RESET_PASSWORD_URL=http://localhost:8000

# ... resto de configuración ...
```

---

## 🚀 **Configuración en Producción**

```bash
# Conectarse al servidor
ssh marketclub-admin-dev@srv829831.hstgr.cloud

# Ir al proyecto
cd htdocs/admin-dev.marketclub.com.co

# Editar .env
nano .env
```

Asegúrate de tener:

```env
APP_NAME=MarketClub
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://admin-dev.marketclub.com.co

FRONTEND_URL=https://market-club-frontend.vercel.app
RESET_PASSWORD_URL=https://admin-dev.marketclub.com.co

# ... resto de configuración ...
```

**Guardar:** `CTRL + O`, `ENTER`, `CTRL + X`

**Limpiar cache:**

```bash
php artisan config:clear
php artisan config:cache
```

---

## ✅ **Verificar la Configuración**

```bash
php artisan tinker
```

Ejecutar:

```php
config('app.url')
// Local: "http://localhost:8000"
// Producción: "https://admin-dev.marketclub.com.co"

config('app.frontend_url')
// Local: "http://localhost:3000"
// Producción: "https://market-club-frontend.vercel.app"

config('app.reset_password_url')
// Local: "http://localhost:8000"
// Producción: "https://admin-dev.marketclub.com.co"

exit
```

---

## 🧪 **Probar Reset de Contraseña**

### **1. Desde el frontend, solicitar reset**

Local: `http://localhost:3000/forgot-password`
Producción: `https://market-club-frontend.vercel.app/forgot-password`

### **2. El email debe contener un enlace como:**

Local: `http://localhost:8000/reset-password?token=...`
Producción: `https://admin-dev.marketclub.com.co/reset-password?token=...`

### **3. Al hacer clic, debe abrir:**

La página del **backend** (Laravel Blade) con el formulario de cambio de contraseña.

---

## ⚠️ **IMPORTANTE**

### **✅ CORRECTO:**

```env
APP_URL=https://admin-dev.marketclub.com.co
RESET_PASSWORD_URL=https://admin-dev.marketclub.com.co
```

### **❌ INCORRECTO:**

```env
APP_URL=https://market-club-frontend.vercel.app  # ← No!
RESET_PASSWORD_URL=https://market-club-frontend.vercel.app  # ← No!
```

**Regla simple:**

-   `RESET_PASSWORD_URL` SIEMPRE debe apuntar al **backend** (Laravel)
-   `FRONTEND_URL` siempre apunta al **frontend** (React/Vercel)

---

## 🔍 **Solución de Problemas**

### **Problema: El email llega con URL del frontend**

**Solución:**

1. Verificar que `RESET_PASSWORD_URL` esté configurado correctamente
2. Limpiar cache: `php artisan config:clear && php artisan config:cache`
3. Verificar con tinker: `config('app.reset_password_url')`

### **Problema: Múltiples líneas de APP_URL en .env**

```bash
# Ver cuántas veces aparece
grep "APP_URL" .env

# Si hay duplicados, editar manualmente
nano .env
# Dejar solo una línea de APP_URL
```

---

¡Listo! Con esto tu configuración quedará perfecta. 🎉
