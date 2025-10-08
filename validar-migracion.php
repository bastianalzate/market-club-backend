#!/usr/bin/env php
<?php

/**
 * Script para validar que la migración de imágenes fue exitosa
 * 
 * Uso: php validar-migracion.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "\n";
echo "========================================\n";
echo "  VALIDACIÓN DE MIGRACIÓN DE IMÁGENES  \n";
echo "========================================\n\n";

$errores = 0;
$warnings = 0;

// Test 1: Verificar que existe el directorio de uploads
echo "📁 Test 1: Verificando directorio public/uploads/products/\n";
if (is_dir(public_path('uploads/products'))) {
    echo "   ✅ Directorio existe\n\n";
} else {
    echo "   ❌ ERROR: Directorio no existe\n\n";
    $errores++;
}

// Test 2: Contar archivos en public/uploads/products
echo "📊 Test 2: Contando archivos migrados\n";
$uploadedFiles = File::allFiles(public_path('uploads/products'));
$totalUploaded = count($uploadedFiles);
echo "   📈 Total de archivos en public/uploads/products/: {$totalUploaded}\n";

if ($totalUploaded > 0) {
    echo "   ✅ Hay archivos en el directorio\n\n";
} else {
    echo "   ⚠️  WARNING: No hay archivos en el directorio\n\n";
    $warnings++;
}

// Test 3: Verificar productos en la base de datos
echo "📋 Test 3: Verificando productos en la base de datos\n";
$totalProducts = DB::table('products')->count();
$productsWithImages = DB::table('products')->whereNotNull('image')->count();
$productsWithNewPath = DB::table('products')->where('image', 'like', 'uploads/%')->count();
$productsWithOldPath = DB::table('products')->where('image', 'like', 'products/%')->count();

echo "   📊 Total de productos: {$totalProducts}\n";
echo "   🖼️  Productos con imagen: {$productsWithImages}\n";
echo "   ✅ Productos con ruta nueva (uploads/...): {$productsWithNewPath}\n";
echo "   ⚠️  Productos con ruta antigua (products/...): {$productsWithOldPath}\n";

if ($productsWithOldPath > 0) {
    echo "   ⚠️  WARNING: Hay productos con rutas antiguas\n\n";
    $warnings++;
} else {
    echo "   ✅ Todas las rutas fueron actualizadas\n\n";
}

// Test 4: Verificar que los archivos existen físicamente
echo "🔍 Test 4: Verificando que los archivos existen físicamente\n";
$products = DB::table('products')
    ->whereNotNull('image')
    ->select('id', 'name', 'image')
    ->limit(10)
    ->get();

$archivosEncontrados = 0;
$archivosNoEncontrados = 0;

foreach ($products as $product) {
    $path = public_path($product->image);
    
    if (file_exists($path)) {
        $archivosEncontrados++;
    } else {
        $archivosNoEncontrados++;
        if ($archivosNoEncontrados <= 3) {
            echo "   ❌ No encontrado: {$product->image}\n";
        }
    }
}

echo "   ✅ Archivos encontrados: {$archivosEncontrados}/{$products->count()}\n";
if ($archivosNoEncontrados > 0) {
    echo "   ❌ Archivos NO encontrados: {$archivosNoEncontrados}\n";
    $errores++;
} else {
    echo "   ✅ Todos los archivos existen\n";
}
echo "\n";

// Test 5: Verificar URLs generadas por el modelo
echo "🔗 Test 5: Verificando URLs generadas\n";
$product = App\Models\Product::whereNotNull('image')->first();

if ($product) {
    echo "   Producto de prueba: {$product->name}\n";
    echo "   Ruta en BD: {$product->image}\n";
    echo "   URL generada: {$product->image_url}\n";
    
    if (str_contains($product->image_url, 'uploads/')) {
        echo "   ✅ URL correcta (contiene 'uploads/')\n\n";
    } else {
        echo "   ❌ ERROR: URL no contiene 'uploads/'\n\n";
        $errores++;
    }
} else {
    echo "   ⚠️  WARNING: No hay productos con imagen para probar\n\n";
    $warnings++;
}

// Test 6: Verificar permisos
echo "🔒 Test 6: Verificando permisos del directorio\n";
$uploadsDir = public_path('uploads/products');
$perms = substr(sprintf('%o', fileperms($uploadsDir)), -4);
echo "   Permisos actuales: {$perms}\n";

if ($perms >= '0755') {
    echo "   ✅ Permisos correctos (lectura para todos)\n\n";
} else {
    echo "   ⚠️  WARNING: Los permisos podrían ser restrictivos\n";
    echo "   💡 Ejecuta: chmod -R 755 public/uploads\n\n";
    $warnings++;
}

// Test 7: Verificar que existe .htaccess
echo "⚙️  Test 7: Verificando archivos de configuración\n";
$htaccessExists = file_exists(public_path('uploads/.htaccess'));
$gitignoreExists = file_exists(public_path('uploads/.gitignore'));

echo "   .htaccess: " . ($htaccessExists ? "✅ Existe" : "⚠️  No existe") . "\n";
echo "   .gitignore: " . ($gitignoreExists ? "✅ Existe" : "⚠️  No existe") . "\n\n";

if (!$htaccessExists) $warnings++;
if (!$gitignoreExists) $warnings++;

// Test 8: Comparar archivos entre storage y uploads
echo "📊 Test 8: Comparando archivos entre storage y uploads\n";
$storageDir = storage_path('app/public/products');

if (is_dir($storageDir)) {
    $storageFiles = File::allFiles($storageDir);
    $totalStorage = count($storageFiles);
    
    echo "   📦 Archivos en storage/app/public/products/: {$totalStorage}\n";
    echo "   📦 Archivos en public/uploads/products/: {$totalUploaded}\n";
    
    if ($totalStorage == $totalUploaded) {
        echo "   ✅ Todos los archivos fueron copiados\n\n";
    } else {
        $diferencia = abs($totalStorage - $totalUploaded);
        echo "   ⚠️  WARNING: Diferencia de {$diferencia} archivos\n\n";
        $warnings++;
    }
} else {
    echo "   ℹ️  No existe el directorio de storage (normal si ya se limpió)\n\n";
}

// Test 9: Verificar rutas web
echo "🌐 Test 9: Verificando rutas web\n";
$routes = app('router')->getRoutes();
$uploadRouteExists = false;

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'uploads/{path}')) {
        $uploadRouteExists = true;
        break;
    }
}

if ($uploadRouteExists) {
    echo "   ✅ Ruta /uploads/{path} configurada\n\n";
} else {
    echo "   ❌ ERROR: No se encontró la ruta /uploads/{path}\n\n";
    $errores++;
}

// Test 10: Simular acceso a archivo
echo "📸 Test 10: Probando acceso a archivos\n";
if (count($uploadedFiles) > 0) {
    $testFile = $uploadedFiles[0];
    $relativePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $testFile->getPathname());
    $relativePath = str_replace('\\', '/', $relativePath);
    
    echo "   Archivo de prueba: {$relativePath}\n";
    
    if (file_exists(public_path($relativePath))) {
        echo "   ✅ Archivo accesible\n";
        $url = asset($relativePath);
        echo "   🔗 URL: {$url}\n\n";
    } else {
        echo "   ❌ ERROR: Archivo no accesible\n\n";
        $errores++;
    }
} else {
    echo "   ⚠️  No hay archivos para probar\n\n";
}

// Resumen final
echo "========================================\n";
echo "           RESUMEN DE VALIDACIÓN        \n";
echo "========================================\n\n";

if ($errores == 0 && $warnings == 0) {
    echo "🎉 ✅ TODO PERFECTO - Sin errores ni warnings\n\n";
    echo "La migración se completó exitosamente.\n";
    echo "El sistema está listo para producción.\n\n";
} elseif ($errores == 0) {
    echo "✅ VALIDACIÓN EXITOSA - {$warnings} warning(s)\n\n";
    echo "La migración se completó correctamente.\n";
    echo "Hay algunas advertencias menores que puedes revisar.\n\n";
} else {
    echo "❌ SE ENCONTRARON ERRORES - {$errores} error(es), {$warnings} warning(s)\n\n";
    echo "Por favor, revisa los errores arriba antes de continuar.\n\n";
}

echo "========================================\n\n";

// Sugerencias
if ($errores > 0 || $warnings > 0) {
    echo "💡 SUGERENCIAS:\n\n";
    
    if ($productsWithOldPath > 0) {
        echo "   • Ejecuta nuevamente: php migrate-images-to-public.php\n";
    }
    
    if ($warnings > 0 && str_contains(PHP_OS, 'WIN') === false) {
        echo "   • Ajusta permisos: chmod -R 755 public/uploads\n";
    }
    
    echo "\n";
}

exit($errores > 0 ? 1 : 0);

