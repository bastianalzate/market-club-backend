<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload a product image
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('image');
            
            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Crear directorio si no existe (directamente en public/)
            $folder = 'uploads/products/' . date('Y/m');
            $destinationPath = public_path($folder);
            
            // Asegurarse de que el directorio exista
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            // Guardar el archivo directamente en public/
            $file->move($destinationPath, $fileName);
            
            // Ruta relativa para guardar en BD y generar URL
            $path = $folder . '/' . $fileName;
            $url = asset($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path, // Ruta relativa para guardar en BD
                'filename' => $fileName,
                'message' => 'Imagen subida exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product image
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            // Construir ruta completa
            $fullPath = public_path($request->path);
            
            // Verificar que el archivo existe
            if (file_exists($fullPath)) {
                unlink($fullPath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen eliminada exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'La imagen no existe'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all uploaded images for selection
     */
    public function index()
    {
        try {
            $images = [];
            $directory = public_path('uploads/products');
            
            if (!is_dir($directory)) {
                return response()->json([
                    'success' => true,
                    'images' => []
                ]);
            }
            
            // Obtener todos los archivos recursivamente
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $relativePath = str_replace('\\', '/', $relativePath);
                    
                    $images[] = [
                        'path' => $relativePath,
                        'url' => asset($relativePath),
                        'name' => $file->getFilename(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime(),
                    ];
                }
            }
            
            // Ordenar por fecha de modificación (más recientes primero)
            usort($images, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });
            
            return response()->json([
                'success' => true,
                'images' => $images
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las imágenes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve an image file directly
     * Sirve archivos desde public/uploads/
     */
    public function serve($path)
    {
        // Sanitize the path to prevent directory traversal
        $path = str_replace(['..', '\\'], '', $path);
        
        // Construir ruta completa
        $fullPath = public_path('uploads/' . $path);
        
        // Check if file exists
        if (!file_exists($fullPath)) {
            abort(404, 'Image not found');
        }

        // Get mime type
        $mimeType = mime_content_type($fullPath);

        // Return the image with proper headers
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
        ]);
    }
}
