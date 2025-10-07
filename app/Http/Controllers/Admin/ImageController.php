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
            
            // Crear directorio si no existe
            $directory = 'products/' . date('Y/m');
            
            // Guardar la imagen
            $path = $file->storeAs($directory, $fileName, 'public');
            
            // Generar URL pública
            $url = Storage::disk('public')->url($path);
            
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
            // Verificar que el archivo existe
            if (Storage::disk('public')->exists($request->path)) {
                Storage::disk('public')->delete($request->path);
                
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
            $files = Storage::disk('public')->allFiles('products');
            
            foreach ($files as $file) {
                $images[] = [
                    'path' => $file,
                    'url' => Storage::disk('public')->url($file),
                    'name' => basename($file),
                    'size' => Storage::disk('public')->size($file),
                    'modified' => Storage::disk('public')->lastModified($file),
                ];
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
     * Serve an image file directly (fallback if symlink doesn't work)
     * Use this route if the storage:link doesn't work in production
     */
    public function serve($path)
    {
        // Sanitize the path to prevent directory traversal
        $path = str_replace(['..', '\\'], '', $path);
        
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Image not found');
        }

        // Get file content and mime type
        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        // Return the image with proper headers
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year
    }
}
