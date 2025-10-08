#!/bin/bash
#
# Quick Fix Script - Market Club Production
# Soluciona problemas de Git ownership y despliega la aplicación
#

echo "========================================"
echo "Market Club - Quick Fix Script"
echo "========================================"
echo ""

# Variables
PROJECT_DIR="/home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co"
USER="marketclub-admin-dev"
WEB_USER="www-data"

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}1. Solucionando problema de Git ownership...${NC}"
cd "$PROJECT_DIR"
sudo chown -R $USER:$USER .
echo -e "${GREEN}✓ Propietario corregido${NC}"
echo ""

echo -e "${YELLOW}2. Verificando Git...${NC}"
if git status > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Git funcionando correctamente${NC}"
else
    echo -e "${RED}✗ Error: Git aún tiene problemas${NC}"
    exit 1
fi
echo ""

echo -e "${YELLOW}3. Actualizando código desde Git...${NC}"
git pull origin main
echo -e "${GREEN}✓ Código actualizado${NC}"
echo ""

echo -e "${YELLOW}4. Instalando dependencias...${NC}"
composer install --no-dev --optimize-autoloader > /dev/null 2>&1
echo -e "${GREEN}✓ Dependencias instaladas${NC}"
echo ""

echo -e "${YELLOW}5. Ejecutando migraciones...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"
echo ""

echo -e "${YELLOW}6. Limpiando y optimizando cachés...${NC}"
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
php artisan view:cache > /dev/null 2>&1
echo -e "${GREEN}✓ Cachés optimizados${NC}"
echo ""

echo -e "${YELLOW}7. Configurando storage (enlace simbólico para imágenes)...${NC}"
# Eliminar enlace anterior si existe
if [ -L "public/storage" ]; then
    rm public/storage
fi
php artisan storage:link
echo -e "${GREEN}✓ Storage configurado${NC}"
echo ""

echo -e "${YELLOW}8. Ajustando permisos de Laravel...${NC}"
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $WEB_USER:$WEB_USER storage bootstrap/cache
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    sudo chown -R $WEB_USER:$WEB_USER public/storage
fi
echo -e "${GREEN}✓ Permisos ajustados${NC}"
echo ""

echo "========================================"
echo -e "${GREEN}✓ Despliegue completado exitosamente${NC}"
echo "========================================"
echo ""
echo "Verificación:"
echo "1. Git status:"
git status | head -3
echo ""
echo "2. Storage link:"
ls -la public/storage 2>/dev/null || echo "   (enlace no encontrado)"
echo ""
echo "3. Verifica las imágenes en:"
echo "   https://admin-dev.marketclub.com.co/storage/products/2025/09/"
echo ""
echo "4. Visita el sitio:"
echo "   https://admin-dev.marketclub.com.co"
echo ""


