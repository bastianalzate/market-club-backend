<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wholesaler;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class WholesalerController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {
        $query = User::query();

        // Solo mostrar usuarios que son mayoristas
        $query->where('is_wholesaler', true);

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtro por país
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filtro por estado (usando is_active en lugar de status)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'enabled');
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $wholesalers = $query->paginate(10);

        return view('admin.wholesalers.index', compact('wholesalers'));
    }

    // Los métodos create y store no son necesarios ya que los mayoristas
    // se registran a través del sistema de usuarios normales con is_wholesaler = true

    public function show(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        return view('admin.wholesalers.show', compact('wholesaler'));
    }

    public function edit(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        return view('admin.wholesalers.edit', compact('wholesaler'));
    }

    public function update(Request $request, User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $wholesaler->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $wholesaler->update($request->only(['name', 'email', 'phone', 'country', 'is_active']));

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista actualizado exitosamente.');
    }

    public function approve(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $wholesaler->update([
            'is_active' => true,
        ]);

        // Enviar email de activación
        $emailSent = $this->emailService->sendWholesalerActivationEmailForUser($wholesaler);

        $message = $emailSent
            ? 'Mayorista habilitado exitosamente. Se generó una nueva contraseña y se envió el correo de activación con las credenciales.'
            : 'Mayorista habilitado exitosamente, pero hubo un problema al enviar el correo con las credenciales.';

        return redirect()->route('admin.wholesalers.index')
            ->with('success', $message);
    }

    public function toggleStatus(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        $newStatus = !$wholesaler->is_active;
        
        $wholesaler->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'habilitado' : 'deshabilitado';

        // Si se habilita, enviar email de activación
        $emailSent = false;
        if ($newStatus === true) {
            $emailSent = $this->emailService->sendWholesalerActivationEmailForUser($wholesaler);
        }

        return response()->json([
            'success' => true,
            'message' => "Mayorista {$statusText} exitosamente." . ($newStatus && $emailSent ? ' Se generó una nueva contraseña y se envió el correo de activación con las credenciales.' : ''),
            'status' => $newStatus ? 'enabled' : 'disabled',
            'status_text' => $statusText,
            'email_sent' => $emailSent,
        ]);
    }

    /**
     * Habilitar mayorista del modelo Wholesaler y enviar email de activación
     */
    public function activateWholesaler(Wholesaler $wholesaler)
    {
        try {
            // Actualizar estado del mayorista
            $wholesaler->update([
                'status' => 'enabled',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            // Enviar email de activación
            $emailSent = $this->emailService->sendWholesalerActivationEmail($wholesaler);

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mayorista habilitado exitosamente y email de activación enviado.',
                    'email_sent' => true
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Mayorista habilitado exitosamente, pero hubo un problema al enviar el email.',
                    'email_sent' => false
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al habilitar mayorista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deshabilitar mayorista del modelo Wholesaler
     */
    public function deactivateWholesaler(Wholesaler $wholesaler)
    {
        try {
            $wholesaler->update([
                'status' => 'disabled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mayorista deshabilitado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al deshabilitar mayorista: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $wholesaler)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }
        
        // Verificar si el usuario tiene órdenes
        if ($wholesaler->orders()->count() > 0) {
            return redirect()->route('admin.wholesalers.index')
                ->with('error', 'No se puede eliminar el mayorista porque tiene órdenes asociadas.');
        }

        $wholesaler->delete();

        return redirect()->route('admin.wholesalers.index')
            ->with('success', 'Mayorista eliminado exitosamente.');
    }

    /**
     * Servir archivos de mayoristas de forma segura
     */
    public function serveFile(User $wholesaler, $filename)
    {
        // Verificar que el usuario sea un mayorista
        if (!$wholesaler->is_wholesaler) {
            abort(404, 'Mayorista no encontrado');
        }

        // Verificar que el archivo existe y pertenece al mayorista
        if (!$wholesaler->wholesaler_document_path || !Storage::disk('local')->exists($wholesaler->wholesaler_document_path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Obtener la ruta completa del archivo desde storage local (private)
        $filePath = Storage::disk('local')->path($wholesaler->wholesaler_document_path);
        
        // Verificar que el archivo existe físicamente
        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        // Determinar el tipo MIME
        $mimeType = mime_content_type($filePath);
        
        // Verificar que es un tipo de archivo permitido (PDF o imagen)
        $allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedTypes)) {
            abort(403, 'Tipo de archivo no permitido');
        }

        // Servir el archivo desde storage private
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $wholesaler->wholesaler_document_original_name . '"'
        ]);
    }
}

