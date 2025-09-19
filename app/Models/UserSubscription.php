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
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'next_billing_date' => 'date',
        'metadata' => 'array',
        'cancelled_at' => 'datetime',
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
        ]);

        return true;
    }
}
