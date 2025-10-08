#!/usr/bin/env php
<?php

/**
 * Script para migrar imágenes de storage/app/public/products/ a public/uploads/products/
 * y actualizar las rutas en la base de datos
 * 
 * Uso: php migrate-images-to-public.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "===========================================\n";
echo "Migración de Imágenes a public/uploads/\n";
echo "===========================================\n\n";

// Rutas
$sourceDir = storage_path('app/public/products');
$destinationDir = public_path('uploads/products');

// Verificar que existe el directorio de origen
if (!is_dir($sourceDir)) {
    echo "❌ No existe el directorio de origen: {$sourceDir}\n";
    echo "No hay nada que migrar.\n";
    exit(0);
}

// Crear directorio de destino si no existe
if (!is_dir($destinationDir)) {
    echo "📁 Creando directorio de destino: {$destinationDir}\n";
    File::makeDirectory($destinationDir, 0755, true);
}

// Obtener todos los archivos del directorio de origen
echo "🔍 Buscando archivos en: {$sourceDir}\n\n";

$files = File::allFiles($sourceDir);
$totalFiles = count($files);
$copiedFiles = 0;
$errorFiles = 0;

echo "📊 Archivos encontrados: {$totalFiles}\n\n";

if ($totalFiles === 0) {
    echo "No hay archivos para migrar.\n";
    exit(0);
}

echo "Copiando archivos...\n";
echo str_repeat('-', 50) . "\n";

foreach ($files as $file) {
    try {
        // Obtener ruta relativa
        $relativePath = str_replace($sourceDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $relativePath = str_replace('\\', '/', $relativePath);
        
        // Construir ruta de destino
        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $relativePath;
        $destinationPathDir = dirname($destinationPath);
        
        // Crear subdirectorios si no existen
        if (!is_dir($destinationPathDir)) {
            File::makeDirectory($destinationPathDir, 0755, true);
        }
        
        // Copiar archivo
        if (File::copy($file->getPathname(), $destinationPath)) {
            $copiedFiles++;
            echo "✓ {$relativePath}\n";
        } else {
            $errorFiles++;
            echo "✗ Error copiando: {$relativePath}\n";
        }
        
    } catch (Exception $e) {
        $errorFiles++;
        echo "✗ Error: {$e->getMessage()}\n";
    }
}

echo str_repeat('-', 50) . "\n";
echo "✅ Archivos copiados: {$copiedFiles}\n";
if ($errorFiles > 0) {
    echo "❌ Errores: {$errorFiles}\n";
}
echo "\n";

// Actualizar rutas en la base de datos
echo "🔄 Actualizando rutas en la base de datos...\n";
echo str_repeat('-', 50) . "\n";

try {
    // Actualizar productos
    $updatedProducts = DB::table('products')
        ->where('image', 'like', 'products/%')
        ->update([
            'image' => DB::raw("CONCAT('uploads/', image)")
        ]);
    
    echo "✓ Productos actualizados: {$updatedProducts}\n";
    
    // Actualizar galería de productos (campo JSON)
    $productsWithGallery = DB::table('products')
        ->whereNotNull('gallery')
        ->where('gallery', '!=', 'null')
        ->get();
    
    $updatedGalleries = 0;
    foreach ($productsWithGallery as $product) {
        $gallery = json_decode($product->gallery, true);
        
        if (is_array($gallery) && !empty($gallery)) {
            $updated = false;
            $newGallery = array_map(function($path) use (&$updated) {
                if (is_string($path) && !str_starts_with($path, 'uploads/')) {
                    $updated = true;
                    return 'uploads/' . $path;
                }
                return $path;
            }, $gallery);
            
            if ($updated) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['gallery' => json_encode($newGallery)]);
                $updatedGalleries++;
            }
        }
    }
    
    echo "✓ Galerías actualizadas: {$updatedGalleries}\n";
    
} catch (Exception $e) {
    echo "❌ Error actualizando base de datos: {$e->getMessage()}\n";
}

echo str_repeat('-', 50) . "\n\n";

echo "===========================================\n";
echo "✅ Migración completada exitosamente\n";
echo "===========================================\n\n";

echo "📝 Próximos pasos:\n";
echo "1. Verifica que las imágenes funcionan en tu aplicación\n";
echo "2. Una vez confirmado, puedes eliminar:\n";
echo "   - storage/app/public/products/ (imágenes antiguas)\n";
echo "   - public/storage (enlace simbólico ya no necesario)\n\n";

echo "⚠️  IMPORTANTE: No elimines los archivos hasta confirmar que todo funciona bien\n\n";

