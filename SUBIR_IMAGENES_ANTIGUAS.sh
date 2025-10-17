#!/bin/bash

echo "📸 Subiendo imágenes antiguas al servidor..."
echo "==========================================="

# 1. Crear archivo comprimido en local
echo "1️⃣ Comprimiendo imágenes en local..."
cd public/uploads/products
tar -czf imagenes-productos.tar.gz 2025/

echo "   ✅ Archivo creado: imagenes-productos.tar.gz"
echo "   📏 Tamaño: $(du -h imagenes-productos.tar.gz | cut -f1)"

echo ""
echo "2️⃣ Instrucciones para subir al servidor:"
echo ""
echo "OPCIÓN A - Usando SCP:"
echo "   scp imagenes-productos.tar.gz marketclub-admin-dev@srv829831.hstgr.cloud:~/htdocs/admin-dev.marketclub.com.co/public/uploads/products/"
echo ""
echo "OPCIÓN B - Usando el panel de control del hosting:"
echo "   1. Ir al panel de control de tu hosting"
echo "   2. Navegar a: ~/htdocs/admin-dev.marketclub.com.co/public/uploads/products/"
echo "   3. Subir el archivo: imagenes-productos.tar.gz"
echo ""
echo "OPCIÓN C - Usando SFTP:"
echo "   sftp marketclub-admin-dev@srv829831.hstgr.cloud"
echo "   cd htdocs/admin-dev.marketclub.com.co/public/uploads/products/"
echo "   put imagenes-productos.tar.gz"
echo "   quit"
echo ""
echo "3️⃣ Comandos para ejecutar EN EL SERVIDOR después de subir:"
echo ""
echo "   cd ~/htdocs/admin-dev.marketclub.com.co/public/uploads/products/"
echo "   tar -xzf imagenes-productos.tar.gz"
echo "   rm imagenes-productos.tar.gz"
echo "   php ~/htdocs/admin-dev.marketclub.com.co/verificar-imagenes.php"
echo ""
echo "4️⃣ Verificar que funcionó:"
echo "   https://admin-dev.marketclub.com.co/admin/products"







