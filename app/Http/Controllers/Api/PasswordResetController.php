<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Solicitar reset de contraseña
     */
    public function requestReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        // Verificar si el usuario existe
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Por seguridad, devolver éxito aunque el email no exista
            return response()->json([
                'success' => true,
                'message' => 'Si el email existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.',
            ]);
        }

        try {
            // Crear token de reset
            $passwordReset = PasswordReset::createOrUpdateToken($email);
            
            // Enviar email con el enlace de reset (directamente al backend)
            $resetUrl = config('app.url') . '/reset-password?token=' . $passwordReset->token;
            
            Log::info("Generated reset URL: {$resetUrl}");
            
            $this->emailService->sendPasswordResetEmail($user, $resetUrl);
            
            Log::info("Password reset requested for user {$user->id} with email {$email}");
            
            return response()->json([
                'success' => true,
                'message' => 'Si el email existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.',
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset request error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud. Intenta nuevamente.',
            ], 500);
        }
    }

    /**
     * Verificar token de reset
     */
    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Token requerido',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->token;

        if (!PasswordReset::isValidToken($token)) {
            return response()->json([
                'success' => false,
                'message' => 'El enlace de restablecimiento no es válido o ha expirado.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token válido',
        ]);
    }

    /**
     * Resetear contraseña
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->token;
        $password = $request->password;

        // Verificar que el token sea válido
        if (!PasswordReset::isValidToken($token)) {
            return response()->json([
                'success' => false,
                'message' => 'El enlace de restablecimiento no es válido o ha expirado.',
            ], 400);
        }

        try {
            // Obtener el registro de reset
            $passwordReset = PasswordReset::where('token', $token)
                ->where('used', false)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'El enlace de restablecimiento no es válido o ya ha sido usado.',
                ], 400);
            }

            // Buscar el usuario
            $user = User::where('email', $passwordReset->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.',
                ], 404);
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($password),
            ]);

            // Marcar token como usado
            PasswordReset::markAsUsed($token);

            Log::info("Password reset completed for user {$user->id} with email {$user->email}");

            return response()->json([
                'success' => true,
                'message' => 'Contraseña restablecida exitosamente.',
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer la contraseña. Intenta nuevamente.',
            ], 500);
        }
    }
}