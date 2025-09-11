<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'wompi_transaction_id',
        'reference',
        'payment_method',
        'amount',
        'currency',
        'status',
        'wompi_status',
        'wompi_response',
        'customer_data',
        'payment_url',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'wompi_response' => 'array',
        'customer_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Relación con la orden
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Verificar si la transacción está aprobada
     */
    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    /**
     * Verificar si la transacción está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Verificar si la transacción fue rechazada
     */
    public function isDeclined(): bool
    {
        return in_array($this->status, ['DECLINED', 'VOIDED']);
    }

    /**
     * Obtener el estado legible
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'APPROVED' => 'Aprobado',
            'PENDING' => 'Pendiente',
            'DECLINED' => 'Rechazado',
            'VOIDED' => 'Anulado',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener el método de pago legible
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'CARD' => 'Tarjeta de Crédito/Débito',
            'PSE' => 'PSE',
            'NEQUI' => 'Nequi',
            'BANCOLOMBIA_TRANSFER' => 'Transferencia Bancolombia',
            default => $this->payment_method,
        };
    }
}