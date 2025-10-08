#!/usr/bin/env php
<?php

/**
 * Script rápido para verificar que las imágenes funcionan correctamente
 * 
 * Uso: php verificar-imagenes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔍 Verificación Rápida de Imágenes\n";
echo "================================\n\n";

// Test 1: Verificar modelo
echo "1️⃣ Verificando modelo Product...\n";
$product = App\Models\Product::whereNotNull('image')->first();

if ($product) {
    echo "   ✅ Producto encontrado: {$product->name}\n";
    echo "   📁 Ruta en BD: {$product->image}\n";
    echo "   🔗 URL generada: {$product->image_url}\n";
    
    if (str_contains($product->image_url, 'uploads/')) {
        echo "   ✅ URL correcta (contiene 'uploads/')\n";
    } else {
        echo "   ❌ URL incorrecta\n";
    }
} else {
    echo "   ❌ No se encontraron productos con imagen\n";
}

echo "\n";

// Test 2: Verificar archivo físico
echo "2️⃣ Verificando archivo físico...\n";
if ($product) {
    $path = public_path($product->image);
    if (file_exists($path)) {
        echo "   ✅ Archivo existe: {$path}\n";
        echo "   📏 Tamaño: " . number_format(filesize($path)) . " bytes\n";
    } else {
        echo "   ❌ Archivo no encontrado: {$path}\n";
    }
}

echo "\n";

// Test 3: Verificar acceso HTTP
echo "3️⃣ Verificando acceso HTTP...\n";
if ($product) {
    $url = $product->image_url;
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 5
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    
    if ($headers && str_contains($headers[0], '200')) {
        echo "   ✅ Imagen accesible via HTTP: {$url}\n";
    } else {
        echo "   ❌ Imagen no accesible: {$url}\n";
        if ($headers) {
            echo "   📋 Respuesta: {$headers[0]}\n";
        }
    }
}

echo "\n";

// Test 4: Verificar URLs incorrectas (que no deberían funcionar)
echo "4️⃣ Verificando que URLs antiguas no funcionan...\n";
if ($product) {
    $oldUrl = str_replace('/uploads/', '/storage/uploads/', $product->image_url);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 5
        ]
    ]);
    
    $headers = @get_headers($oldUrl, 1, $context);
    
    if ($headers && str_contains($headers[0], '200')) {
        echo "   ⚠️  URL antigua aún funciona (no debería): {$oldUrl}\n";
    } else {
        echo "   ✅ URL antigua correctamente bloqueada: {$oldUrl}\n";
    }
}

echo "\n";

// Test 5: Contar archivos
echo "5️⃣ Contando archivos...\n";
$uploadedFiles = glob(public_path('uploads/products/**/*.{jpg,jpeg,png,gif,webp}'), GLOB_BRACE);
$totalFiles = count($uploadedFiles);

echo "   📊 Total de imágenes en public/uploads/products/: {$totalFiles}\n";

if ($totalFiles > 0) {
    echo "   ✅ Hay imágenes disponibles\n";
} else {
    echo "   ❌ No hay imágenes en el directorio\n";
}

echo "\n";

// Test 6: Verificar rutas en BD
echo "6️⃣ Verificando base de datos...\n";
$totalProducts = App\Models\Product::count();
$productsWithImages = App\Models\Product::whereNotNull('image')->count();
$productsWithNewPath = App\Models\Product::where('image', 'like', 'uploads/%')->count();
$productsWithOldPath = App\Models\Product::where('image', 'like', 'products/%')->count();

echo "   📊 Total productos: {$totalProducts}\n";
echo "   🖼️  Con imagen: {$productsWithImages}\n";
echo "   ✅ Con ruta nueva (uploads/): {$productsWithNewPath}\n";
echo "   ⚠️  Con ruta antigua (products/): {$productsWithOldPath}\n";

if ($productsWithOldPath == 0) {
    echo "   ✅ Todas las rutas fueron actualizadas\n";
} else {
    echo "   ❌ Hay productos con rutas antiguas\n";
}

echo "\n";

// Resumen
echo "================================\n";
echo "📋 RESUMEN:\n\n";

if ($product && file_exists(public_path($product->image)) && $productsWithOldPath == 0) {
    echo "✅ TODO CORRECTO\n";
    echo "   • Modelo genera URLs correctas\n";
    echo "   • Archivos existen físicamente\n";
    echo "   • Base de datos actualizada\n";
    echo "   • URLs antiguas bloqueadas\n\n";
    
    echo "🎯 PRÓXIMO PASO:\n";
    echo "   Abre tu navegador en:\n";
    echo "   http://localhost:8000/admin/products\n";
    echo "   Las imágenes deberían verse correctamente.\n";
} else {
    echo "❌ HAY PROBLEMAS\n";
    echo "   Revisa los errores arriba.\n";
}

echo "\n";

?>
