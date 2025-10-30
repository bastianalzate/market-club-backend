# 🔐 Guía Frontend - Restablecimiento de Contraseña

## ❌ **PROBLEMA ACTUAL**

El frontend está haciendo la petición a:

```
http://localhost:3000/undefined/password/request-reset
                      ^^^^^^^^^ ERROR!
```

Esto significa que la **URL base de la API no está definida** en el frontend.

---

## ✅ **SOLUCIÓN**

### **1. Configurar la URL base de la API**

En el frontend, asegúrate de tener configurada la URL base:

```javascript
// .env (frontend)
REACT_APP_API_URL=http://localhost:8000/api
# O en producción:
REACT_APP_API_URL=https://admin-dev.marketclub.com.co/api
```

```javascript
// config/api.js o similar
const API_BASE_URL =
    process.env.REACT_APP_API_URL || "http://localhost:8000/api";
export default API_BASE_URL;
```

---

## 📋 **ENDPOINTS CORRECTOS**

### **1. Solicitar Reset de Contraseña**

**Endpoint:** `POST /api/password/request-reset`

**Request:**

```json
{
    "email": "usuario@ejemplo.com"
}
```

**Response (Éxito):**

```json
{
    "success": true,
    "message": "Si el email existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña."
}
```

**Response (Error):**

```json
{
    "success": false,
    "message": "Email inválido",
    "errors": {
        "email": ["El campo email es requerido."]
    }
}
```

---

### **2. Verificar Token (Opcional)**

**Endpoint:** `POST /api/password/verify-token`

**Request:**

```json
{
    "token": "abc123..."
}
```

**Response (Token válido):**

```json
{
    "success": true,
    "message": "Token válido"
}
```

**Response (Token inválido):**

```json
{
    "success": false,
    "message": "El enlace de restablecimiento no es válido o ha expirado."
}
```

---

### **3. Resetear Contraseña**

**Endpoint:** `POST /api/password/reset`

**Request:**

```json
{
    "token": "abc123...",
    "password": "nuevaContraseña123",
    "password_confirmation": "nuevaContraseña123"
}
```

**Response (Éxito):**

```json
{
    "success": true,
    "message": "Contraseña restablecida exitosamente."
}
```

**Response (Error):**

```json
{
    "success": false,
    "message": "Datos inválidos",
    "errors": {
        "password": ["La contraseña debe tener al menos 8 caracteres."]
    }
}
```

---

## 🔄 **FLUJO COMPLETO**

### **Flujo 1: Reset desde el Frontend (Recomendado si tienes página de reset)**

```
1. Usuario ingresa email
   └─ Frontend → POST /api/password/request-reset
      └─ Backend envía email con enlace

2. Usuario hace clic en el enlace del email
   └─ Redirige a: http://localhost:3000/reset-password?token=abc123...
      └─ Frontend captura el token de la URL

3. Usuario ingresa nueva contraseña
   └─ Frontend → POST /api/password/reset
      └─ Backend actualiza la contraseña

4. Frontend redirige al login
```

---

### **Flujo 2: Reset desde el Backend (Actual - Más Simple)**

```
1. Usuario ingresa email
   └─ Frontend → POST /api/password/request-reset
      └─ Backend envía email con enlace

2. Usuario hace clic en el enlace del email
   └─ Redirige a: https://admin-dev.marketclub.com.co/reset-password?token=abc123...
      └─ Se abre una página en el backend (Laravel Blade)

3. Usuario ingresa nueva contraseña en el backend
   └─ Backend procesa el formulario directamente
   └─ Backend muestra página de éxito

4. Usuario puede volver al frontend manualmente
```

---

## 💻 **CÓDIGO DE EJEMPLO (Frontend - React)**

### **Página: Solicitar Reset**

```javascript
// pages/ForgotPassword.jsx
import { useState } from "react";
import API_BASE_URL from "../config/api";

function ForgotPassword() {
    const [email, setEmail] = useState("");
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage("");
        setError("");

        try {
            const response = await fetch(
                `${API_BASE_URL}/password/request-reset`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ email }),
                }
            );

            const data = await response.json();

            if (data.success) {
                setMessage(data.message);
                setEmail(""); // Limpiar el input
            } else {
                setError(data.message || "Error al enviar el correo");
            }
        } catch (err) {
            setError("Error de conexión. Intenta nuevamente.");
            console.error("Error:", err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="forgot-password-container">
            <h2>Olvidé mi Contraseña</h2>

            {message && <div className="alert alert-success">{message}</div>}

            {error && <div className="alert alert-error">{error}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="tu@email.com"
                        required
                    />
                </div>

                <button type="submit" disabled={loading}>
                    {loading ? "Enviando..." : "Enviar Enlace de Reset"}
                </button>
            </form>

            <p className="info-text">
                Recibirás un email con un enlace para restablecer tu contraseña.
            </p>
        </div>
    );
}

export default ForgotPassword;
```

---

### **Página: Reset Password (Opcional)**

```javascript
// pages/ResetPassword.jsx
import { useState, useEffect } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import API_BASE_URL from "../config/api";

function ResetPassword() {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const token = searchParams.get("token");

    const [password, setPassword] = useState("");
    const [passwordConfirmation, setPasswordConfirmation] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [validToken, setValidToken] = useState(null);

    // Verificar token al cargar
    useEffect(() => {
        if (!token) {
            setError("Token no encontrado");
            setValidToken(false);
            return;
        }

        verifyToken();
    }, [token]);

    const verifyToken = async () => {
        try {
            const response = await fetch(
                `${API_BASE_URL}/password/verify-token`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ token }),
                }
            );

            const data = await response.json();
            setValidToken(data.success);

            if (!data.success) {
                setError(data.message);
            }
        } catch (err) {
            setError("Error al verificar el token");
            setValidToken(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (password !== passwordConfirmation) {
            setError("Las contraseñas no coinciden");
            return;
        }

        if (password.length < 8) {
            setError("La contraseña debe tener al menos 8 caracteres");
            return;
        }

        setLoading(true);
        setError("");

        try {
            const response = await fetch(`${API_BASE_URL}/password/reset`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    token,
                    password,
                    password_confirmation: passwordConfirmation,
                }),
            });

            const data = await response.json();

            if (data.success) {
                alert("Contraseña restablecida exitosamente");
                navigate("/login");
            } else {
                setError(data.message || "Error al restablecer la contraseña");
            }
        } catch (err) {
            setError("Error de conexión. Intenta nuevamente.");
            console.error("Error:", err);
        } finally {
            setLoading(false);
        }
    };

    if (validToken === false) {
        return (
            <div className="reset-password-container">
                <h2>Enlace Inválido</h2>
                <p className="error">{error}</p>
                <button onClick={() => navigate("/forgot-password")}>
                    Solicitar Nuevo Enlace
                </button>
            </div>
        );
    }

    if (validToken === null) {
        return <div>Verificando token...</div>;
    }

    return (
        <div className="reset-password-container">
            <h2>Restablecer Contraseña</h2>

            {error && <div className="alert alert-error">{error}</div>}

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="password">Nueva Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="Mínimo 8 caracteres"
                        required
                        minLength={8}
                    />
                </div>

                <div className="form-group">
                    <label htmlFor="password-confirmation">
                        Confirmar Contraseña
                    </label>
                    <input
                        type="password"
                        id="password-confirmation"
                        value={passwordConfirmation}
                        onChange={(e) =>
                            setPasswordConfirmation(e.target.value)
                        }
                        placeholder="Repite la contraseña"
                        required
                        minLength={8}
                    />
                </div>

                <button type="submit" disabled={loading}>
                    {loading ? "Guardando..." : "Restablecer Contraseña"}
                </button>
            </form>
        </div>
    );
}

export default ResetPassword;
```

---

## 🎨 **RUTAS EN EL FRONTEND**

```javascript
// App.jsx o routes.jsx
import ForgotPassword from "./pages/ForgotPassword";
import ResetPassword from "./pages/ResetPassword";

<Routes>
    <Route path="/forgot-password" element={<ForgotPassword />} />
    <Route path="/reset-password" element={<ResetPassword />} />
    {/* ... otras rutas ... */}
</Routes>;
```

---

## 🔍 **DEBUGGING**

### **1. Verificar la URL base**

```javascript
console.log("API_BASE_URL:", API_BASE_URL);
// Debería mostrar: http://localhost:8000/api
// NO: undefined
```

### **2. Verificar la petición en DevTools**

Abrir **Network Tab** en Chrome DevTools y verificar:

-   ✅ URL correcta: `http://localhost:8000/api/password/request-reset`
-   ✅ Method: `POST`
-   ✅ Status: `200 OK`
-   ✅ Request Payload: `{ "email": "..." }`

---

## 📧 **EMAIL QUE RECIBIRÁ EL USUARIO**

El usuario recibirá un email con un enlace como:

```
https://admin-dev.marketclub.com.co/reset-password?token=abc123...
```

Este enlace lo llevará a una **página en el backend** (Laravel Blade), no al frontend.

---

## 🚀 **RECOMENDACIÓN**

Si quieres que el usuario se quede en el frontend durante todo el proceso, necesitas:

1. Modificar el backend para que el enlace apunte al frontend:

    ```php
    // app/Http/Controllers/Api/PasswordResetController.php
    $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $passwordReset->token;
    ```

2. Agregar en `.env` del backend:

    ```env
    FRONTEND_URL=http://localhost:3000
    # O en producción:
    FRONTEND_URL=https://marketclub.com.co
    ```

3. Agregar en `config/app.php`:
    ```php
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
    ```

---

## ✅ **CHECKLIST PARA EL FRONTEND**

-   [ ] Verificar que `API_BASE_URL` esté definido correctamente
-   [ ] No tiene el valor `undefined`
-   [ ] Incluye `/api` al final (sin barra final)
-   [ ] La petición se hace a: `${API_BASE_URL}/password/request-reset`
-   [ ] El método es `POST`
-   [ ] El header `Content-Type: application/json` está presente
-   [ ] El body incluye `{ "email": "..." }`

---

## 🧪 **PROBAR CON CURL**

```bash
# Probar el endpoint directamente
curl -X POST http://localhost:8000/api/password/request-reset \
  -H "Content-Type: application/json" \
  -d '{"email":"bastianmurilloalzate@gmail.com"}'

# Respuesta esperada:
# {"success":true,"message":"Si el email existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña."}
```

---

¡Con esta guía el frontend debería poder integrar correctamente el restablecimiento de contraseña! 🎉
