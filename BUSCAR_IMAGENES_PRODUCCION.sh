#!/bin/bash

echo "🔍 Buscando imágenes en producción..."
echo "====================================="

# Buscar imágenes en diferentes ubicaciones
echo "1️⃣ Buscando en storage/app/public/products/:"
find storage/app/public/products -type f 2>/dev/null | wc -l
if [ $? -eq 0 ]; then
    echo "   Archivos encontrados en storage/app/public/products/"
    find storage/app/public/products -type f 2>/dev/null | head -5
else
    echo "   ❌ No existe storage/app/public/products/"
fi

echo ""

echo "2️⃣ Buscando en public/uploads/products/:"
find public/uploads/products -type f 2>/dev/null | wc -l
if [ $? -eq 0 ]; then
    echo "   Archivos encontrados en public/uploads/products/"
    find public/uploads/products -type f 2>/dev/null | head -5
else
    echo "   ❌ No existe public/uploads/products/"
fi

echo ""

echo "3️⃣ Buscando en public/storage/products/:"
find public/storage/products -type f 2>/dev/null | wc -l
if [ $? -eq 0 ]; then
    echo "   Archivos encontrados en public/storage/products/"
    find public/storage/products -type f 2>/dev/null | head -5
else
    echo "   ❌ No existe public/storage/products/"
fi

echo ""

echo "4️⃣ Buscando cualquier archivo .png, .jpg, .jpeg en public/:"
find public/ -name "*.png" -o -name "*.jpg" -o -name "*.jpeg" 2>/dev/null | head -10

echo ""

echo "5️⃣ Verificando base de datos:"
php artisan tinker --execute="
echo 'Total productos: ' . App\Models\Product::count() . PHP_EOL;
echo 'Productos con imagen: ' . App\Models\Product::whereNotNull('image')->count() . PHP_EOL;
echo 'Con ruta uploads/: ' . App\Models\Product::where('image', 'like', 'uploads/%')->count() . PHP_EOL;
echo 'Con ruta products/: ' . App\Models\Product::where('image', 'like', 'products/%')->count() . PHP_EOL;
echo 'Con ruta storage/: ' . App\Models\Product::where('image', 'like', 'storage/%')->count() . PHP_EOL;
"

echo ""

echo "6️⃣ Verificando enlace simbólico:"
ls -la public/storage 2>/dev/null || echo "   ❌ No existe enlace simbólico public/storage"

echo ""

echo "7️⃣ Verificando permisos:"
ls -la storage/ 2>/dev/null | head -3
ls -la public/ 2>/dev/null | head -3




