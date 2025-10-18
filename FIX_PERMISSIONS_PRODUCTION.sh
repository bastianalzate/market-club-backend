#!/bin/bash

# Script para arreglar permisos en producción antes de hacer git pull

echo "🔧 Arreglando permisos en producción..."

# 1. Cambiar propietario de todo el directorio al usuario correcto
echo "📁 Cambiando propietario del directorio..."
sudo chown -R marketclub-admin-dev-ssh:marketclub-admin-dev-ssh ~/htdocs/admin-dev.marketclub.com.co/

# 2. Dar permisos de escritura al usuario
echo "🔒 Ajustando permisos..."
chmod -R 755 ~/htdocs/admin-dev.marketclub.com.co/

# 3. Dar permisos especiales a storage y bootstrap/cache
echo "📦 Ajustando permisos de storage y cache..."
chmod -R 775 ~/htdocs/admin-dev.marketclub.com.co/storage
chmod -R 775 ~/htdocs/admin-dev.marketclub.com.co/bootstrap/cache

# 4. Limpiar cachés de Laravel
echo "🧹 Limpiando cachés..."
cd ~/htdocs/admin-dev.marketclub.com.co/
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Recrear directorios de storage si no existen
echo "📁 Recreando directorios de storage..."
mkdir -p storage/app/private
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# 6. Ajustar permisos de los directorios recién creados
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

echo "✅ Permisos arreglados. Ahora puedes hacer git pull."







