<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'token',
        'used'
    ];

    protected $casts = [
        'used' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Generar token único para reset de contraseña
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Crear o actualizar token de reset
     */
    public static function createOrUpdateToken(string $email): self
    {
        // Marcar tokens anteriores como usados
        self::where('email', $email)->update(['used' => true]);
        
        // Crear nuevo token
        return self::create([
            'email' => $email,
            'token' => self::generateToken(),
            'used' => false,
        ]);
    }

    /**
     * Verificar si el token es válido y no ha sido usado
     */
    public static function isValidToken(string $token): bool
    {
        return self::where('token', $token)
            ->where('used', false)
            ->where('created_at', '>', now()->subHours(24)) // Token válido por 24 horas
            ->exists();
    }

    /**
     * Marcar token como usado
     */
    public static function markAsUsed(string $token): bool
    {
        return self::where('token', $token)->update(['used' => true]);
    }
}
