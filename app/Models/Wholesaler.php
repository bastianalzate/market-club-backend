<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wholesaler extends Model
{
    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'tax_id',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'business_type',
        'business_description',
        'status',
        'notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que aprobó al mayorista
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope para mayoristas habilitados
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', 'enabled');
    }

    /**
     * Scope para mayoristas deshabilitados
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', 'disabled');
    }

    /**
     * Verificar si el mayorista está habilitado
     */
    public function isEnabled(): bool
    {
        return $this->status === 'enabled';
    }

    /**
     * Verificar si el mayorista está deshabilitado
     */
    public function isDisabled(): bool
    {
        return $this->status === 'disabled';
    }

    /**
     * Obtener el nombre completo del negocio con información de contacto
     */
    public function getFullBusinessInfoAttribute(): string
    {
        return "{$this->business_name} - {$this->contact_name}";
    }

    /**
     * Obtener la dirección completa
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address;
        if ($this->city) {
            $address .= ", {$this->city}";
        }
        if ($this->state) {
            $address .= ", {$this->state}";
        }
        if ($this->country) {
            $address .= ", {$this->country}";
        }
        return $address;
    }
}
