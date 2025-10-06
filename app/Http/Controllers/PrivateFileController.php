<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileController extends Controller
{
    /**
     * Serve private files with authentication check
     *
     * @param Request $request
     * @param string $path
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function serve(Request $request, string $path)
    {
        // Decode the path in case it's URL encoded
        $path = urldecode($path);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'No autorizado');
        }

        $user = Auth::user();

        // Check if the file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Security check: Only allow access to wholesaler documents
        // and only if the user is the owner or is an admin
        if (strpos($path, 'wholesaler-documents') !== 0) {
            abort(403, 'Acceso denegado');
        }

        // Additional security: Check if user owns this document or is admin
        if (!$user->isAdmin() && !$this->userOwnsDocument($user, $path)) {
            abort(403, 'No tienes permisos para acceder a este archivo');
        }

        // Get file info
        $fileInfo = Storage::disk('local')->getMetadata($path);
        $mimeType = $fileInfo['mimetype'] ?? 'application/octet-stream';
        $filename = basename($path);

        // Return the file as a streamed response
        return Storage::disk('local')->response($path, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    /**
     * Check if the authenticated user owns the document
     *
     * @param \App\Models\User $user
     * @param string $path
     * @return bool
     */
    private function userOwnsDocument($user, string $path): bool
    {
        // Check if this document belongs to the user
        return $user->wholesaler_document_path === $path;
    }

    /**
     * Download private files with authentication check
     *
     * @param Request $request
     * @param string $path
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function download(Request $request, string $path)
    {
        // Decode the path in case it's URL encoded
        $path = urldecode($path);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'No autorizado');
        }

        $user = Auth::user();

        // Check if the file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Security check: Only allow access to wholesaler documents
        if (strpos($path, 'wholesaler-documents') !== 0) {
            abort(403, 'Acceso denegado');
        }

        // Additional security: Check if user owns this document or is admin
        if (!$user->isAdmin() && !$this->userOwnsDocument($user, $path)) {
            abort(403, 'No tienes permisos para acceder a este archivo');
        }

        // Get the original filename from the database if available
        $filename = $user->wholesaler_document_original_name ?? basename($path);

        // Return the file as a download
        return Storage::disk('local')->download($path, $filename);
    }
}