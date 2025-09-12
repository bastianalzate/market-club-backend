<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_snapshot',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array',
    ];

    /**
     * Relación con el carrito
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtener el precio actual del producto
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->product->current_price;
    }

    /**
     * Verificar si el precio ha cambiado
     */
    public function hasPriceChanged(): bool
    {
        return $this->unit_price !== $this->current_price;
    }

    /**
     * Actualizar precio si ha cambiado
     */
    public function updatePriceIfChanged()
    {
        if ($this->hasPriceChanged()) {
            $this->update([
                'unit_price' => $this->current_price,
                'total_price' => $this->quantity * $this->current_price,
                'product_snapshot' => $this->product->toArray(),
            ]);
        }

        return $this;
    }
}