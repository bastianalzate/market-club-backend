#!/bin/bash

# Script para diagnosticar problemas de imágenes en producción
# Ejecutar este script en el servidor de producción

echo "========================================="
echo "Diagnóstico de Imágenes en Producción"
echo "========================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

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

# 1. Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "Este script debe ejecutarse desde el directorio raíz del proyecto Laravel"
    exit 1
fi

print_success "Directorio de proyecto encontrado"
echo ""

# 2. Verificar enlace simbólico
print_info "Verificando enlace simbólico de storage..."
if [ -L "public/storage" ]; then
    print_success "Enlace simbólico existe"
    LINK_TARGET=$(readlink -f public/storage)
    print_info "Enlace apunta a: $LINK_TARGET"
    
    # Verificar que el enlace funciona
    if [ -d "public/storage" ]; then
        print_success "Enlace simbólico funciona correctamente"
    else
        print_error "Enlace simbólico roto"
    fi
else
    print_error "Enlace simbólico NO existe"
    print_info "Ejecuta: php artisan storage:link"
fi
echo ""

# 3. Verificar estructura de directorios
print_info "Verificando estructura de directorios..."

if [ -d "storage/app/public" ]; then
    print_success "Directorio storage/app/public existe"
else
    print_error "Directorio storage/app/public NO existe"
fi

if [ -d "storage/app/public/products" ]; then
    print_success "Directorio de productos existe"
    PRODUCT_COUNT=$(find storage/app/public/products -type f | wc -l)
    print_info "Número de archivos de productos: $PRODUCT_COUNT"
else
    print_error "Directorio de productos NO existe"
fi

if [ -d "public/storage" ]; then
    print_success "Directorio public/storage existe"
else
    print_error "Directorio public/storage NO existe"
fi
echo ""

# 4. Verificar permisos
print_info "Verificando permisos..."

# Verificar permisos de storage
if [ -r "storage/app/public" ]; then
    print_success "storage/app/public es legible"
else
    print_error "storage/app/public NO es legible"
fi

if [ -w "storage/app/public" ]; then
    print_success "storage/app/public es escribible"
else
    print_error "storage/app/public NO es escribible"
fi

# Verificar permisos de public/storage
if [ -r "public/storage" ]; then
    print_success "public/storage es legible"
else
    print_error "public/storage NO es legible"
fi
echo ""

# 5. Verificar propietarios
print_info "Verificando propietarios de directorios..."
echo "Propietario de storage: $(ls -ld storage | awk '{print $3":"$4}')"
echo "Propietario de public/storage: $(ls -ld public/storage | awk '{print $3":"$4}')"
echo ""

# 6. Verificar archivos de imagen específicos
print_info "Verificando archivos de imagen específicos..."

# Buscar archivos en el directorio de octubre 2025
if [ -d "storage/app/public/products/2025/10" ]; then
    print_success "Directorio de octubre 2025 existe"
    OCTOBER_COUNT=$(find storage/app/public/products/2025/10 -type f | wc -l)
    print_info "Archivos en octubre 2025: $OCTOBER_COUNT"
    
    # Listar algunos archivos
    echo "Algunos archivos encontrados:"
    ls -la storage/app/public/products/2025/10/ | head -5
else
    print_warning "Directorio de octubre 2025 no existe"
fi
echo ""

# 7. Verificar configuración de Laravel
print_info "Verificando configuración de Laravel..."

# Verificar configuración de storage
STORAGE_CONFIG=$(php artisan tinker --execute="echo config('filesystems.disks.public.root');")
print_info "Configuración de storage público: $STORAGE_CONFIG"

# Verificar URL de storage
STORAGE_URL=$(php artisan tinker --execute="echo config('filesystems.disks.public.url');")
print_info "URL de storage público: $STORAGE_URL"
echo ""

# 8. Verificar ruta de fallback
print_info "Verificando ruta de fallback..."

# Verificar que la ruta existe en routes/web.php
if grep -q "Route::get('/storage/{path}'" routes/web.php; then
    print_success "Ruta de fallback configurada"
else
    print_error "Ruta de fallback NO configurada"
fi

# Verificar que el controlador existe
if grep -q "ImageController" routes/web.php; then
    print_success "ImageController referenciado en rutas"
else
    print_error "ImageController NO referenciado en rutas"
fi
echo ""

# 9. Probar acceso a una imagen específica
print_info "Probando acceso a imagen específica..."

# Buscar una imagen para probar
TEST_IMAGE=$(find storage/app/public/products -name "*.png" -o -name "*.jpg" -o -name "*.jpeg" | head -1)

if [ -n "$TEST_IMAGE" ]; then
    print_info "Imagen de prueba encontrada: $TEST_IMAGE"
    
    # Convertir ruta a URL
    RELATIVE_PATH=${TEST_IMAGE#storage/app/public/}
    TEST_URL="https://admin-dev.marketclub.com.co/storage/$RELATIVE_PATH"
    print_info "URL de prueba: $TEST_URL"
    
    # Probar acceso HTTP
    print_info "Probando acceso HTTP..."
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$TEST_URL")
    
    if [ "$HTTP_STATUS" = "200" ]; then
        print_success "Imagen accesible via HTTP (Status: $HTTP_STATUS)"
    else
        print_error "Imagen NO accesible via HTTP (Status: $HTTP_STATUS)"
    fi
else
    print_warning "No se encontraron imágenes para probar"
fi
echo ""

# 10. Resumen y recomendaciones
echo "========================================="
echo "RESUMEN Y RECOMENDACIONES"
echo "========================================="

if [ ! -L "public/storage" ]; then
    print_error "PROBLEMA PRINCIPAL: Enlace simbólico faltante"
    echo "SOLUCIÓN:"
    echo "  php artisan storage:link"
    echo "  chmod -R 775 storage"
    echo "  sudo chown -R www-data:www-data storage public/storage"
    echo ""
fi

if [ ! -d "storage/app/public/products" ]; then
    print_error "PROBLEMA: Directorio de productos no existe"
    echo "SOLUCIÓN:"
    echo "  mkdir -p storage/app/public/products"
    echo ""
fi

if [ "$HTTP_STATUS" != "200" ]; then
    print_error "PROBLEMA: Imágenes no accesibles via HTTP"
    echo "POSIBLES SOLUCIONES:"
    echo "  1. Verificar configuración de nginx/apache"
    echo "  2. Verificar permisos del servidor web"
    echo "  3. Limpiar caché del navegador"
    echo ""
fi

echo "Para más detalles, consulta:"
echo "  - PRODUCTION_IMAGE_FIX.md"
echo "  - QUICK_FIX_IMAGENES.md"
echo ""
echo "Script completado."




