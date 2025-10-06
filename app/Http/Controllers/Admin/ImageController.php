<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload an image (product or category)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'type' => 'nullable|in:product,category', // Tipo de imagen
        ]);

        try {
            $file = $request->file('image');
            $type = $request->get('type', 'product'); // Por defecto es producto
            
            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Crear directorio según el tipo
            $directory = $type === 'category' ? 'categories' : 'products/' . date('Y/m');
            
            // Guardar la imagen
            $path = $file->storeAs($directory, $fileName, 'public');
            
            // Generar URL pública
            $url = asset('storage/' . $path);
            
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
                    'url' => asset('storage/' . $file),
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
}
