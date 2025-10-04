<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Crear un nuevo contacto desde el formulario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'profession' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'first_name.required' => 'El primer nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede exceder 2000 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Limpiar el número de teléfono si se proporciona
            $phone = $request->phone;
            if ($phone) {
                // Remover espacios, guiones y paréntesis
                $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
                // Si empieza con +57, mantenerlo; si no, agregarlo si es un número colombiano
                if (!str_starts_with($phone, '+') && strlen($phone) === 10) {
                    $phone = '+57' . $phone;
                }
            }

            $contact = Contact::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $phone,
                'date_of_birth' => $request->date_of_birth,
                'profession' => $request->profession,
                'message' => $request->message,
                'status' => 'new',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tu mensaje ha sido enviado exitosamente. Te contactaremos pronto.',
                'data' => [
                    'id' => $contact->id,
                    'reference' => 'CONTACT-' . str_pad($contact->id, 6, '0', STR_PAD_LEFT),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor intenta nuevamente.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de contactos (para uso interno/admin)
     */
    public function getStats()
    {
        try {
            $stats = [
                'total' => Contact::count(),
                'new' => Contact::new()->count(),
                'in_progress' => Contact::inProgress()->count(),
                'resolved' => Contact::resolved()->count(),
                'today' => Contact::whereDate('created_at', today())->count(),
                'this_week' => Contact::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }
}
