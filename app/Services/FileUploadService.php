<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the private storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $allowedExtensions
     * @param int $maxSizeInMB
     * @return array
     * @throws \Exception
     */
    public function uploadPrivateFile(
        UploadedFile $file,
        string $directory = 'wholesaler-documents',
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'],
        int $maxSizeInMB = 5
    ): array {
        // Validate file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Tipo de archivo no permitido. Solo se permiten: " . implode(', ', $allowedExtensions));
        }

        // Validate file size (convert MB to bytes)
        $maxSizeInBytes = $maxSizeInMB * 1024 * 1024;
        if ($file->getSize() > $maxSizeInBytes) {
            throw new \Exception("El archivo es demasiado grande. Tamaño máximo: {$maxSizeInMB}MB");
        }

        // Validate MIME type for additional security
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'application/pdf'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception("Tipo de archivo no válido");
        }

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $extension;
        
        // Store file in private directory
        $path = $file->storeAs($directory, $filename, 'local');

        return [
            'path' => $path,
            'original_name' => $originalName,
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }

    /**
     * Delete a file from private storage
     *
     * @param string $path
     * @return bool
     */
    public function deletePrivateFile(string $path): bool
    {
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->delete($path);
        }
        return false;
    }

    /**
     * Get file URL for private files (requires authentication)
     *
     * @param string $path
     * @return string|null
     */
    public function getPrivateFileUrl(string $path): ?string
    {
        if (Storage::disk('local')->exists($path)) {
            return route('private-file', ['path' => $path]);
        }
        return null;
    }

    /**
     * Validate image file specifically
     *
     * @param UploadedFile $file
     * @param int $maxSizeInMB
     * @return bool
     * @throws \Exception
     */
    public function validateImage(UploadedFile $file, int $maxSizeInMB = 5): bool
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Solo se permiten imágenes JPG, JPEG y PNG");
        }

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception("Tipo de archivo de imagen no válido");
        }

        $maxSizeInBytes = $maxSizeInMB * 1024 * 1024;
        if ($file->getSize() > $maxSizeInBytes) {
            throw new \Exception("La imagen es demasiado grande. Tamaño máximo: {$maxSizeInMB}MB");
        }

        return true;
    }
}
