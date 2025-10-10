<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'price_paid',
        'starts_at',
        'ends_at',
        'next_billing_date',
        'metadata',
        'cancelled_at',
        'payment_token',
        'payment_method_type',
        'last_four_digits',
        'auto_renew',
        'payment_retry_count',
        'last_payment_attempt_at',
        'last_payment_error',
        'suspended_at',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'next_billing_date' => 'date',
        'metadata' => 'array',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'last_payment_attempt_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el plan de suscripción
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Scope para suscripciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
    }

    /**
     * Scope para suscripciones que expiran pronto
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
                    ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
               && $this->starts_at <= now() 
               && $this->ends_at >= now();
    }

    /**
     * Verificar si la suscripción ha expirado
     */
    public function isExpired(): bool
    {
        return $this->ends_at < now();
    }

    /**
     * Obtener días restantes de la suscripción
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return (int) now()->diffInDays($this->ends_at, false);
    }

    /**
     * Cancelar suscripción
     */
    public function cancel(): bool
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return true;
    }

    /**
     * Renovar suscripción
     */
    public function renew(int $months = 1): bool
    {
        $newEndDate = $this->isActive() 
            ? $this->ends_at->addMonths($months)
            : now()->addMonths($months);

        $this->update([
            'status' => 'active',
            'ends_at' => $newEndDate,
            'next_billing_date' => $newEndDate,
            'cancelled_at' => null,
            'suspended_at' => null,
            'payment_retry_count' => 0,
            'last_payment_error' => null,
        ]);

        return true;
    }

    /**
     * Scope para suscripciones pendientes de renovación
     */
    public function scopePendingRenewal($query)
    {
        return $query->where('status', 'active')
                    ->where('auto_renew', true)
                    ->where('next_billing_date', '<=', now())
                    ->whereNotNull('payment_token');
    }

    /**
     * Scope para suscripciones con renovación automática
     */
    public function scopeAutoRenewable($query)
    {
        return $query->where('auto_renew', true)
                    ->whereNotNull('payment_token');
    }

    /**
     * Verificar si tiene método de pago guardado
     */
    public function hasPaymentMethod(): bool
    {
        return !empty($this->payment_token);
    }

    /**
     * Verificar si puede intentar cobrar de nuevo
     */
    public function canRetryPayment(): bool
    {
        // Máximo 4 intentos
        if ($this->payment_retry_count >= 4) {
            return false;
        }

        // Si no hay intento previo, puede intentar
        if (!$this->last_payment_attempt_at) {
            return true;
        }

        // Reintentos en días: 1, 3, 5, 7
        $retryDays = [0, 1, 3, 5, 7];
        $daysSinceLastAttempt = now()->diffInDays($this->last_payment_attempt_at);
        
        return $daysSinceLastAttempt >= ($retryDays[$this->payment_retry_count] ?? 7);
    }

    /**
     * Registrar intento de pago fallido
     */
    public function recordFailedPayment(string $error): void
    {
        $this->increment('payment_retry_count');
        $this->update([
            'last_payment_attempt_at' => now(),
            'last_payment_error' => $error,
        ]);

        // Suspender después de 4 intentos fallidos
        if ($this->payment_retry_count >= 4) {
            $this->suspend();
        }
    }

    /**
     * Registrar pago exitoso
     */
    public function recordSuccessfulPayment(): void
    {
        $this->update([
            'payment_retry_count' => 0,
            'last_payment_attempt_at' => now(),
            'last_payment_error' => null,
        ]);
    }

    /**
     * Suspender suscripción por fallo de pago
     */
    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'auto_renew' => false,
        ]);
    }

    /**
     * Reactivar suscripción suspendida
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
            'auto_renew' => true,
            'payment_retry_count' => 0,
            'last_payment_error' => null,
        ]);
    }

    /**
     * Actualizar método de pago
     */
    public function updatePaymentMethod(string $token, string $type, ?string $lastFour = null): void
    {
        $this->update([
            'payment_token' => $token,
            'payment_method_type' => $type,
            'last_four_digits' => $lastFour,
            'auto_renew' => true,
        ]);
    }

    /**
     * Remover método de pago y desactivar renovación automática
     */
    public function removePaymentMethod(): void
    {
        $this->update([
            'payment_token' => null,
            'payment_method_type' => null,
            'last_four_digits' => null,
            'auto_renew' => false,
        ]);
    }

    /**
     * Verificar si está suspendida
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Obtener información enmascarada del método de pago
     */
    public function getMaskedPaymentMethodAttribute(): ?string
    {
        if (!$this->last_four_digits) {
            return null;
        }

        return "**** **** **** {$this->last_four_digits}";
    }
}
