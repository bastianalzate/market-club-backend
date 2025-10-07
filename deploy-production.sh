#!/bin/bash

# Script de despliegue para Market Club Backend
# Este script actualiza el código y configura el almacenamiento de imágenes

echo "========================================="
echo "Market Club - Script de Despliegue"
echo "========================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${NC}ℹ $1${NC}"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "Este script debe ejecutarse desde el directorio raíz del proyecto Laravel"
    exit 1
fi

print_success "Directorio de proyecto encontrado"

# 1. Modo de mantenimiento
print_info "Activando modo de mantenimiento..."
php artisan down --render="errors::503" --retry=60
print_success "Modo de mantenimiento activado"

# 2. Actualizar código desde Git
print_info "Actualizando código desde Git..."
git pull origin main
if [ $? -eq 0 ]; then
    print_success "Código actualizado"
else
    print_warning "No se pudo actualizar desde Git (puede estar ya actualizado)"
fi

# 3. Instalar/actualizar dependencias
print_info "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader
print_success "Dependencias instaladas"

# 4. Ejecutar migraciones
print_info "Ejecutando migraciones de base de datos..."
php artisan migrate --force
print_success "Migraciones ejecutadas"

# 5. Limpiar cachés
print_info "Limpiando cachés..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
print_success "Cachés limpiados"

# 6. Optimizar aplicación
print_info "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
print_success "Aplicación optimizada"

# 7. Crear enlace simbólico de storage (CLAVE PARA IMÁGENES)
print_info "Configurando almacenamiento de imágenes..."

# Verificar si el enlace ya existe
if [ -L "public/storage" ]; then
    print_warning "El enlace simbólico ya existe"
    print_info "Eliminando enlace anterior..."
    rm public/storage
fi

# Crear el enlace
php artisan storage:link

if [ -L "public/storage" ]; then
    print_success "Enlace simbólico de storage creado correctamente"
    
    # Verificar que apunta al lugar correcto
    LINK_TARGET=$(readlink -f public/storage)
    print_info "Enlace apunta a: $LINK_TARGET"
else
    print_error "No se pudo crear el enlace simbólico"
    print_warning "Las imágenes se servirán mediante ruta dinámica (más lento)"
fi

# 8. Ajustar permisos
print_info "Ajustando permisos de directorios..."

# Permisos para storage y bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Si tenemos permisos de sudo, cambiar el propietario
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data storage
    chown -R www-data:www-data bootstrap/cache
    if [ -L "public/storage" ]; then
        chown -R www-data:www-data public/storage
    fi
    print_success "Permisos y propietario configurados"
else
    print_warning "Ejecuta con sudo para cambiar el propietario a www-data"
    print_info "Comando: sudo chown -R www-data:www-data storage bootstrap/cache public/storage"
fi

# 9. Verificar estructura de storage
print_info "Verificando estructura de storage..."

if [ -d "storage/app/public/products" ]; then
    PRODUCT_COUNT=$(find storage/app/public/products -type f | wc -l)
    print_success "Directorio de productos encontrado con $PRODUCT_COUNT archivos"
else
    print_warning "Directorio de productos no encontrado"
    mkdir -p storage/app/public/products
    print_success "Directorio de productos creado"
fi

# 10. Desactivar modo de mantenimiento
print_info "Desactivando modo de mantenimiento..."
php artisan up
print_success "Aplicación en línea"

echo ""
echo "========================================="
echo "✓ Despliegue completado exitosamente"
echo "========================================="
echo ""

# Información de verificación
echo "Pasos de verificación:"
echo "1. Visita: https://admin-dev.marketclub.com.co"
echo "2. Prueba una imagen: https://admin-dev.marketclub.com.co/storage/products/2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png"
echo "3. Verifica el enlace: ls -la public/storage"
echo ""

# Mostrar información del enlace
if [ -L "public/storage" ]; then
    echo "Estado del enlace simbólico:"
    ls -la public/storage
    echo ""
fi

# Sugerencias finales
echo "Si las imágenes aún no funcionan:"
echo "1. Verifica los logs: tail -f storage/logs/laravel.log"
echo "2. Verifica permisos: namei -l storage/app/public/products"
echo "3. Limpia caché del navegador"
echo "4. Consulta el archivo PRODUCTION_IMAGE_FIX.md para más detalles"
echo ""

exit 0

