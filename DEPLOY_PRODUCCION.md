# 🚀 Guía de Despliegue en Producción

## ✅ Cambios Subidos a Git

**Commit realizado:** `6e49744`
**Archivos:** 23 archivos modificados/creados
**Tamaño:** 14.04 MB

---

## 📋 Pasos para Desplegar en Producción

### **Paso 1: Conectar al Servidor**

```bash
ssh usuario@admin-dev.marketclub.com.co
cd /path/to/market-club-backend
```

### **Paso 2: Actualizar Código**

```bash
# Obtener los últimos cambios
git pull origin main

# Verificar que se descargaron los archivos
ls -la migrate-images-to-public.php
ls -la validar-migracion.php
```

### **Paso 3: Ejecutar Migración de Imágenes**

```bash
# Ejecutar el script de migración
php migrate-images-to-public.php
```

**Salida esperada:**

```
===========================================
Migración de Imágenes a public/uploads/
===========================================

📁 Creando directorio de destino: /path/to/public/uploads/products
🔍 Buscando archivos en: /path/to/storage/app/public/products

📊 Archivos encontrados: XXX

Copiando archivos...
--------------------------------------------------
✓ 2025/09/imagen1.png
✓ 2025/09/imagen2.png
...
--------------------------------------------------
✅ Archivos copiados: XXX
❌ Errores: 0

🔄 Actualizando rutas en la base de datos...
--------------------------------------------------
✓ Productos actualizados: XX
✓ Galerías actualizadas: X
--------------------------------------------------

✅ Migración completada exitosamente
```

### **Paso 4: Ajustar Permisos**

```bash
# Dar permisos correctos al directorio uploads
chmod -R 755 public/uploads

# Cambiar propietario al usuario del servidor web
sudo chown -R www-data:www-data public/uploads
```

### **Paso 5: Verificar Migración**

```bash
# Ejecutar script de validación
php validar-migracion.php
```

**Debería mostrar:**

```
🎉 ✅ TODO PERFECTO - Sin errores ni warnings
```

### **Paso 6: Verificar en Navegador**

1. **Abrir:** `https://admin-dev.marketclub.com.co/admin/products`
2. **Verificar:** Las imágenes se ven correctamente
3. **Probar:** Hacer clic en un producto y ver el detalle
4. **Probar:** Subir una nueva imagen

---

## 🔧 Script de Despliegue Completo

Si quieres automatizar todo, puedes crear un script:

```bash
#!/bin/bash
echo "🚀 Iniciando despliegue..."

# 1. Actualizar código
echo "📥 Actualizando código..."
git pull origin main

# 2. Instalar dependencias (si hay cambios)
echo "📦 Instalando dependencias..."
composer install --no-dev --optimize-autoloader

# 3. Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 4. Ejecutar migración de imágenes
echo "📸 Migrando imágenes..."
php migrate-images-to-public.php

# 5. Ajustar permisos
echo "🔒 Ajustando permisos..."
chmod -R 755 public/uploads
sudo chown -R www-data:www-data public/uploads

# 6. Verificar
echo "✅ Verificando migración..."
php validar-migracion.php

echo "🎉 Despliegue completado!"
```

---

## 🧪 Verificación Post-Despliegue

### **Test 1: Imágenes Existentes**

```bash
curl -I https://admin-dev.marketclub.com.co/uploads/products/2025/09/imagen.png
```

**Resultado esperado:** `HTTP/1.1 200 OK`

### **Test 2: Panel Admin**

-   Abrir: `https://admin-dev.marketclub.com.co/admin/products`
-   ✅ Las imágenes se ven en la lista
-   ✅ Se puede ver el detalle de productos
-   ✅ Se puede editar productos

### **Test 3: Subir Imagen Nueva**

-   Ir a crear/editar producto
-   Subir una nueva imagen
-   ✅ Se guarda en `public/uploads/products/YYYY/mm/`
-   ✅ Se ve correctamente en el frontend

### **Test 4: API**

```bash
curl https://admin-dev.marketclub.com.co/api/products | jq '.data[0].image_url'
```

**Resultado esperado:** URL que contenga `uploads/`

---

## 🐛 Troubleshooting

### **Error: "Permission denied"**

```bash
sudo chown -R www-data:www-data public/uploads
chmod -R 755 public/uploads
```

### **Error: "No such file or directory"**

```bash
mkdir -p public/uploads/products
chmod -R 755 public/uploads
```

### **Error: "Command not found: php"**

```bash
# Usar ruta completa de PHP
/path/to/php migrate-images-to-public.php
```

### **Las imágenes no se ven**

1. Verificar permisos: `ls -la public/uploads/`
2. Verificar archivos: `ls -la public/uploads/products/2025/09/`
3. Verificar BD: `SELECT image FROM products LIMIT 5;`
4. Verificar logs: `tail -f storage/logs/laravel.log`

---

## 📞 Comandos de Emergencia

### **Rollback (si algo sale mal)**

```bash
# Revertir el último commit
git revert HEAD

# Restaurar desde backup
mysql -u usuario -p database_name < backup.sql

# Recrear symlink (método antiguo)
php artisan storage:link
```

### **Verificar Estado**

```bash
# Ver archivos migrados
find public/uploads/products -type f | wc -l

# Ver productos con nuevas rutas
php artisan tinker --execute="echo App\Models\Product::where('image', 'like', 'uploads/%')->count();"

# Verificar acceso a imagen
curl -I https://admin-dev.marketclub.com.co/uploads/products/2025/09/imagen.png
```

---

## ✅ Checklist Final

-   [ ] ✅ Código actualizado (`git pull`)
-   [ ] ✅ Migración ejecutada (`php migrate-images-to-public.php`)
-   [ ] ✅ Permisos ajustados (`chmod -R 755 public/uploads`)
-   [ ] ✅ Validación exitosa (`php validar-migracion.php`)
-   [ ] ✅ Imágenes visibles en panel admin
-   [ ] ✅ Se puede subir imagen nueva
-   [ ] ✅ API devuelve URLs correctas
-   [ ] ✅ Acceso directo a imágenes funciona

---

## 🎯 Resultado Esperado

Después del despliegue:

1. ✅ **Imágenes existentes** se ven correctamente
2. ✅ **Nuevas imágenes** se guardan en `public/uploads/`
3. ✅ **No más problemas** de permisos o symlinks
4. ✅ **Sistema compatible** con cualquier servidor
5. ✅ **Funciona igual** que GuinnessBC

---

## 📝 Notas Importantes

1. **Backup:** Siempre hacer backup antes de migrar
2. **Pruebas:** Probar subir una imagen nueva después del despliegue
3. **Limpieza:** Después de 1 semana sin problemas, eliminar archivos antiguos
4. **Monitoreo:** Revisar logs si hay problemas

---

**¡El sistema estará listo para producción sin problemas de imágenes!**







