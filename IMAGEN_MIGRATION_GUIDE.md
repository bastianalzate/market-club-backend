# 📸 Guía de Migración de Imágenes a public/uploads/

## 🎯 Objetivo

Migrar el sistema de almacenamiento de imágenes de `storage/app/public/` a `public/uploads/` para eliminar la dependencia de enlaces simbólicos y solucionar problemas de permisos en producción.

---

## 🔄 Cambios Realizados

### 1. **ImageController.php**

-   ✅ Método `upload()`: Ahora guarda imágenes en `public/uploads/products/YYYY/mm/`
-   ✅ Método `delete()`: Elimina archivos desde `public/`
-   ✅ Método `index()`: Lista archivos desde `public/uploads/`
-   ✅ Método `serve()`: Sirve archivos desde `public/uploads/`

### 2. **routes/web.php**

-   ✅ Cambió `/storage/{path}` → `/uploads/{path}`
-   ✅ Ya no necesita enlaces simbólicos

### 3. **Product.php (Model)**

-   ✅ `getImageUrlAttribute()` actualizado
-   ✅ Mantiene compatibilidad con rutas antiguas
-   ✅ Detecta automáticamente si la ruta es nueva (`uploads/`) o antigua (`storage/`)

### 4. **Script de Migración**

-   ✅ `migrate-images-to-public.php` creado
-   ✅ Copia archivos automáticamente
-   ✅ Actualiza rutas en la base de datos

---

## 📋 Estructura de Archivos

### Antes (antigua)

```
storage/
  └── app/
      └── public/
          └── products/
              └── 2025/
                  └── 09/
                      └── imagen.jpg

public/
  └── storage/ → ../storage/app/public  ← Enlace simbólico requerido
```

### Después (nueva)

```
public/
  └── uploads/
      └── products/
          └── 2025/
              └── 09/
                  └── imagen.jpg  ← Acceso directo
```

---

## 🚀 Pasos para Desplegar en Producción

### Paso 1: Actualizar el código en el servidor

```bash
# Conectar al servidor
ssh usuario@tu-servidor
cd /path/to/market-club-backend

# Obtener los últimos cambios
git pull origin main
```

### Paso 2: Ejecutar el script de migración

```bash
# Ejecutar el script
php migrate-images-to-public.php
```

**El script hará:**

-   ✅ Copiar todas las imágenes de `storage/app/public/products/` a `public/uploads/products/`
-   ✅ Actualizar las rutas en la base de datos
-   ✅ Mantener la estructura de carpetas por año/mes

**Salida esperada:**

```
===========================================
Migración de Imágenes a public/uploads/
===========================================

📁 Creando directorio de destino: /path/to/public/uploads/products
🔍 Buscando archivos en: /path/to/storage/app/public/products

📊 Archivos encontrados: 45

Copiando archivos...
--------------------------------------------------
✓ 2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png
✓ 2025/09/b5384e52-1d21-549b-b7e2-c732b25b9c57.jpg
...
--------------------------------------------------
✅ Archivos copiados: 45
❌ Errores: 0

🔄 Actualizando rutas en la base de datos...
--------------------------------------------------
✓ Productos actualizados: 15
✓ Galerías actualizadas: 3
--------------------------------------------------

✅ Migración completada exitosamente
```

### Paso 3: Ajustar permisos

```bash
# Asegurar que el servidor web pueda leer los archivos
chmod -R 755 public/uploads
sudo chown -R www-data:www-data public/uploads
```

### Paso 4: Verificar que funciona

Abre tu navegador y verifica que las imágenes se ven correctamente:

```
https://admin-dev.marketclub.com.co/uploads/products/2025/09/imagen.png
```

### Paso 5: Limpiar archivos antiguos (OPCIONAL)

⚠️ **IMPORTANTE: Solo después de confirmar que todo funciona**

```bash
# Eliminar imágenes antiguas de storage
rm -rf storage/app/public/products

# Eliminar enlace simbólico (ya no es necesario)
rm -f public/storage
```

---

## 🧪 Probar en Local Primero

Antes de desplegar en producción, prueba en local:

```bash
# En local
php migrate-images-to-public.php

# Verificar que las imágenes funcionan
php artisan serve

# Abrir http://localhost:8000 y verificar productos
```

---

## 🔍 Verificación Post-Migración

### 1. Verificar archivos copiados

```bash
ls -la public/uploads/products/2025/09/
```

### 2. Verificar rutas en base de datos

```sql
SELECT id, name, image FROM products LIMIT 5;
```

Deberías ver rutas como:

```
uploads/products/2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png
```

### 3. Verificar acceso web

```bash
curl -I https://admin-dev.marketclub.com.co/uploads/products/2025/09/imagen.png
```

Debería devolver: `HTTP/1.1 200 OK`

---

## ✅ Ventajas de este Enfoque

| Aspecto                 | Antes (storage/) | Ahora (public/) |
| ----------------------- | ---------------- | --------------- |
| **Symlink**             | ✅ Requerido     | ❌ No necesario |
| **Permisos**            | Complejo         | Simple          |
| **Compatibilidad**      | Limitada         | Universal       |
| **Hosting compartido**  | ❌ Problemas     | ✅ Funciona     |
| **Facilidad de deploy** | Media            | Alta            |

---

## 🐛 Troubleshooting

### Problema: "Permission denied" al copiar archivos

**Solución:**

```bash
sudo chown -R $USER:$USER storage/app/public
sudo chown -R $USER:$USER public/uploads
```

### Problema: Las imágenes antiguas siguen mostrando rutas de storage/

**Solución:**
Ejecuta el script nuevamente o actualiza manualmente:

```sql
UPDATE products
SET image = CONCAT('uploads/', image)
WHERE image LIKE 'products/%';
```

### Problema: 404 en las imágenes nuevas

**Verificar:**

1. Los archivos existen en `public/uploads/products/`
2. Los permisos son correctos (755)
3. La ruta en la base de datos es correcta

---

## 📝 Notas Importantes

1. ✅ Las nuevas imágenes se guardan automáticamente en `public/uploads/`
2. ✅ El sistema mantiene compatibilidad con rutas antiguas
3. ✅ No es necesario ejecutar `php artisan storage:link` nunca más
4. ⚠️ Asegúrate de hacer backup antes de eliminar archivos antiguos
5. 🔒 Las imágenes son públicas (cualquiera puede acceder con la URL)

---

## 🎯 Checklist de Despliegue

-   [ ] Hacer backup de la base de datos
-   [ ] Hacer backup de `storage/app/public/products/`
-   [ ] Actualizar código en el servidor (`git pull`)
-   [ ] Ejecutar script de migración
-   [ ] Verificar permisos de `public/uploads/`
-   [ ] Probar acceso a imágenes desde el navegador
-   [ ] Verificar panel admin (subir nueva imagen)
-   [ ] Verificar frontend (ver productos)
-   [ ] (Opcional) Eliminar archivos antiguos después de 1 semana

---

## 📞 Soporte

Si tienes problemas durante la migración:

1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Verifica los logs del servidor web (Apache/Nginx)
3. Asegúrate de tener permisos correctos en los directorios

---

## 🔄 Rollback (si algo sale mal)

Si necesitas revertir los cambios:

```bash
# 1. Revertir código
git revert HEAD

# 2. Restaurar base de datos desde backup
mysql -u usuario -p database_name < backup.sql

# 3. Recrear enlace simbólico
php artisan storage:link

# 4. Restaurar archivos de storage si los eliminaste
# (desde backup)
```
