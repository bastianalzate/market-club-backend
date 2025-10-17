#!/bin/bash

echo "🔧 Arreglando enlace simbólico en producción..."
echo "=============================================="

# 1. Verificar el enlace actual (incorrecto)
echo "1️⃣ Enlace actual (incorrecto):"
ls -la public/storage

# 2. Eliminar el enlace incorrecto
echo "2️⃣ Eliminando enlace incorrecto..."
rm -f public/storage

# 3. Verificar si existe storage/app/public
echo "3️⃣ Verificando storage/app/public..."
if [ -d "storage/app/public" ]; then
    echo "   ✅ Existe storage/app/public"
    echo "   📊 Archivos encontrados:"
    find storage/app/public -type f | wc -l
    find storage/app/public -type f | head -5
else
    echo "   ❌ No existe storage/app/public"
fi

# 4. Crear enlace correcto
echo "4️⃣ Creando enlace correcto..."
ln -s ../storage/app/public public/storage

# 5. Verificar el nuevo enlace
echo "5️⃣ Verificando nuevo enlace:"
ls -la public/storage

# 6. Probar acceso a imágenes
echo "6️⃣ Probando acceso a imágenes..."
if [ -d "public/storage/products" ]; then
    echo "   ✅ Ahora existe public/storage/products"
    find public/storage/products -type f | wc -l | xargs echo "   📊 Archivos encontrados:"
    find public/storage/products -type f | head -3
else
    echo "   ❌ Aún no existe public/storage/products"
fi

echo ""
echo "✅ Enlace simbólico arreglado"
echo ""
echo "🎯 Próximos pasos:"
echo "1. Ejecutar: php migrate-images-to-public.php"
echo "2. Verificar: php validar-migracion.php"




