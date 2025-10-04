<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $isWholesaler = $request->boolean('is_wholesaler', false);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => $isWholesaler ? 'nullable|string|min:8' : 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'profession' => 'nullable|string|max:255',
            'nit' => $isWholesaler ? 'required|string|max:20' : 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_wholesaler' => 'boolean',
        ]);

        // Generar contraseña automática para mayoristas
        $password = $isWholesaler 
            ? 'TempPass' . rand(1000, 9999) . '!' 
            : $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'profession' => $request->profession,
            'nit' => $request->nit,
            'country' => $request->country,
            'role' => 'customer',
            'is_active' => $isWholesaler ? false : true, // Mayoristas inactivos hasta aprobación
            'is_wholesaler' => $isWholesaler,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];

        // Agregar información específica para mayoristas
        if ($isWholesaler) {
            $response['message'] = 'Registro exitoso, pronto nos pondremos en contacto contigo.';
            $response['is_wholesaler_pending'] = true;
        }

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
