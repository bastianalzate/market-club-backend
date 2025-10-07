# Solución para Imágenes en Producción

## Problema

Las imágenes no se están viendo en producción pero funcionan en local.

-   Local: `http://localhost:8000/storage/products/...`
-   Producción: `https://admin-dev.marketclub.com.co/storage/products/...` (404)

## Causa

El enlace simbólico de `public/storage` → `storage/app/public` no existe en producción.

---

## Solución 1: Crear enlace simbólico (RECOMENDADA)

### Paso 1: SSH al servidor

```bash
ssh usuario@tu-servidor
cd /path/to/market-club-backend
```

### Paso 2: Ejecutar comando Artisan

```bash
php artisan storage:link
```

Deberías ver: `The [public/storage] link has been connected to [storage/app/public]`

### Paso 3: Verificar permisos

```bash
# Verificar que el enlace existe
ls -la public/storage

# Si necesitas dar permisos a storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Paso 4: Verificar propiedad del directorio (si usas Apache/Nginx)

```bash
# Para Apache
sudo chown -R www-data:www-data storage public/storage

# Para Nginx
sudo chown -R nginx:nginx storage public/storage
```

---

## Solución 2: Si el enlace simbólico no funciona (hosting compartido)

Algunos servidores compartidos no permiten enlaces simbólicos. En ese caso:

### Opción 2A: Mover archivos directamente a public

```bash
# Crear directorio en public
mkdir -p public/products

# Mover o copiar archivos
cp -r storage/app/public/products/* public/products/
```

Luego actualizar la configuración de Laravel (ver código más abajo).

### Opción 2B: Usar una ruta dinámica

Agregar una ruta que sirva los archivos desde storage (ver código más abajo).

---

## Solución 3: Usar S3 o almacenamiento en la nube (PRODUCCIÓN A LARGO PLAZO)

Para aplicaciones en producción, es recomendable usar un servicio como AWS S3, DigitalOcean Spaces, o Cloudinary.

---

## Verificación

Después de aplicar la solución, verifica:

1. **Acceso directo a una imagen:**

    ```
    https://admin-dev.marketclub.com.co/storage/products/2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png
    ```

2. **Verificar estructura de directorios:**

    ```bash
    ls -la public/storage
    ls -la storage/app/public/products
    ```

3. **Verificar permisos:**
    ```bash
    ls -la storage
    ls -la storage/app
    ls -la storage/app/public
    ```

---

## Solución de Emergencia: Código alternativo

**¡IMPORTANTE!** Ya he agregado una ruta alternativa que servirá las imágenes dinámicamente si el enlace simbólico no funciona.

### Cómo funciona la solución alternativa:

1. **Ruta agregada en `routes/web.php`:**

    ```php
    Route::get('/storage/{path}', [ImageController::class, 'serve'])
        ->where('path', '.*')
        ->name('storage.serve');
    ```

2. **Método agregado en `ImageController.php`:**

    - Sirve archivos directamente desde `storage/app/public/`
    - Incluye validación de seguridad contra directory traversal
    - Establece headers de caché apropiados
    - Detecta automáticamente el tipo MIME

3. **Ventajas:**

    - No requiere enlaces simbólicos
    - Funciona en cualquier servidor
    - Compatible con hosting compartido
    - Se activa automáticamente si el enlace no existe

4. **Desventajas:**
    - Un poco más lento que servir archivos estáticos
    - Usa recursos de PHP para cada imagen

### Prioridad de soluciones:

1. **Primera opción:** Ejecutar `php artisan storage:link` (más rápido)
2. **Fallback automático:** Si el enlace no existe, la ruta dinámica se encarga

---

## Comandos de despliegue completos

```bash
# 1. Conectar al servidor
ssh usuario@tu-servidor
cd /path/to/market-club-backend

# 2. Actualizar código desde Git
git pull origin main

# 3. Instalar/actualizar dependencias
composer install --no-dev --optimize-autoloader

# 4. Ejecutar migraciones (si hay)
php artisan migrate --force

# 5. Limpiar y optimizar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Crear enlace simbólico de storage
php artisan storage:link

# 7. Verificar permisos
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache public/storage

# 8. Reiniciar servicios (si aplica)
# Para Apache:
sudo systemctl restart apache2
# Para Nginx + PHP-FPM:
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

---

## Verificación de la solución

### 1. Verificar que el enlace simbólico existe:

```bash
ls -la public/storage
```

Deberías ver algo como:

```
lrwxrwxrwx 1 www-data www-data 36 Oct 7 12:00 storage -> /path/to/storage/app/public
```

### 2. Verificar que las imágenes existen:

```bash
ls -la storage/app/public/products/2025/09/
```

### 3. Probar acceso directo a una imagen:

```bash
curl -I https://admin-dev.marketclub.com.co/storage/products/2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png
```

Deberías recibir un `HTTP/1.1 200 OK` en lugar de `404 Not Found`.

### 4. Verificar permisos:

```bash
# El servidor web debe poder leer estos directorios
namei -l storage/app/public/products
```

---

## Troubleshooting

### Error: "The [public/storage] link already exists"

```bash
# Eliminar el enlace existente y recrearlo
rm public/storage
php artisan storage:link
```

### Error: Permisos denegados

```bash
# Asegurarse de que el usuario del servidor web tenga acceso
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### Error: Las imágenes aún no se ven después de crear el enlace

1. Verificar que el archivo `.htaccess` existe en `public/`
2. Verificar que `mod_rewrite` está habilitado en Apache
3. Verificar la configuración de Nginx si aplica
4. Limpiar caché del navegador
5. Verificar que la URL en la base de datos es correcta

### Si usas Nginx

Asegúrate de que tu configuración incluya:

```nginx
location /storage {
    alias /path/to/market-club-backend/storage/app/public;
}
```

---

## Opción adicional: Usar .htaccess (solo Apache)

Si el enlace simbólico no funciona, puedes agregar esto a `public/.htaccess`:

```apache
# Redireccionar /storage a storage/app/public
RewriteRule ^storage/(.*)$ ../storage/app/public/$1 [L]
```

⚠️ **Nota de seguridad:** Esta opción expone el directorio storage, úsala solo si otras opciones no funcionan.
