# 🔒 Solución - Git Ownership Error en Producción

## ❌ Error

```
fatal: detected dubious ownership in repository at '/home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co'
```

Este error ocurre cuando el usuario que ejecuta Git es diferente al propietario del directorio.

---

## ✅ Solución Rápida (Elige UNA opción)

### **Opción 1: Cambiar propietario del repositorio (RECOMENDADA)**

```bash
# Conectar al servidor
ssh marketclub-admin-dev@tu-servidor

# Cambiar propietario a tu usuario
cd /home/marketclub-admin-dev/htdocs
sudo chown -R marketclub-admin-dev:marketclub-admin-dev admin-dev.marketclub.com.co

# Verificar
cd admin-dev.marketclub.com.co
git status
```

**Ventajas:**

-   ✅ Más segura
-   ✅ Soluciona el problema de raíz
-   ✅ No necesitas configuración adicional

---

### **Opción 2: Agregar como directorio seguro**

```bash
# Si no puedes cambiar el propietario
git config --global --add safe.directory /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co

# Verificar
git status
```

**Ventajas:**

-   ✅ Más rápida
-   ✅ No requiere permisos de sudo

**Desventajas:**

-   ⚠️ Menos segura
-   ⚠️ Solo para el usuario actual

---

## 🚀 Después de corregir, ejecuta:

```bash
cd /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co

# Actualizar código
git pull origin main

# Ejecutar script de despliegue
chmod +x deploy-production.sh
./deploy-production.sh
```

---

## 🔍 Verificación de permisos

### Ver quién es el propietario actual:

```bash
ls -la /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co
```

Deberías ver algo como:

```
drwxr-xr-x  15 marketclub-admin-dev  www-data  4096 Oct  7 12:00 .
drwxr-xr-x   3 marketclub-admin-dev  www-data  4096 Oct  7 11:00 ..
drwxr-xr-x   8 marketclub-admin-dev  www-data  4096 Oct  7 12:00 .git
```

### Ver tu usuario actual:

```bash
whoami
# Debería mostrar: marketclub-admin-dev
```

---

## 🛠️ Solución completa (recomendada para producción)

Este script ajusta todos los permisos correctamente:

```bash
cd /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co

# 1. Cambiar propietario del repositorio a tu usuario
sudo chown -R marketclub-admin-dev:marketclub-admin-dev .

# 2. Ajustar permisos generales
sudo chmod -R 755 .

# 3. Permisos específicos para Laravel (el servidor web necesita escribir aquí)
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 4. Si existe public/storage, también ajustar
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    sudo chown -R www-data:www-data public/storage
fi

# 5. Verificar
git status
ls -la storage
```

---

## 📋 Comandos de despliegue completos (copia y pega)

```bash
#!/bin/bash

# Ir al directorio del proyecto
cd /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co

# Solucionar problema de Git
sudo chown -R marketclub-admin-dev:marketclub-admin-dev .

# Actualizar código
git pull origin main

# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Ejecutar migraciones
php artisan migrate --force

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace de storage
php artisan storage:link

# Ajustar permisos de Laravel
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Si existe public/storage
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    sudo chown -R www-data:www-data public/storage
fi

echo "✅ Despliegue completado"
```

---

## ⚠️ Prevención futura

Para evitar este problema en el futuro:

### 1. Siempre usa tu usuario para Git:

```bash
# Verificar usuario actual
whoami

# Cambiar a tu usuario si es necesario
su - marketclub-admin-dev
```

### 2. No uses sudo con Git:

```bash
# ❌ EVITAR:
sudo git pull

# ✅ CORRECTO:
git pull
```

### 3. Mantén permisos consistentes:

```bash
# Después de cada despliegue
sudo chown -R marketclub-admin-dev:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🆘 Troubleshooting

### Problema: "Permission denied"

```bash
sudo chown -R $USER:$USER .
```

### Problema: "Could not resolve host"

```bash
# Verificar conexión a internet
ping -c 3 github.com

# Verificar configuración de Git
git remote -v
```

### Problema: "Your local changes would be overwritten"

```bash
# Ver qué archivos están modificados
git status

# Opción 1: Descartar cambios locales
git reset --hard HEAD
git pull origin main

# Opción 2: Guardar cambios locales
git stash
git pull origin main
git stash pop
```

---

## 📞 Resumen

1. **Ejecuta:** `sudo chown -R marketclub-admin-dev:marketclub-admin-dev /home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co`
2. **Verifica:** `git status`
3. **Despliega:** `./deploy-production.sh`

¡Listo! 🎉















