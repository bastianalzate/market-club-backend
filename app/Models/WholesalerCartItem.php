<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WholesalerCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wholesaler_cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_snapshot',
        'is_wholesaler_item',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array',
        'is_wholesaler_item' => 'boolean',
    ];

    /**
     * Relación con el carrito de mayorista
     */
    public function wholesalerCart(): BelongsTo
    {
        return $this->belongsTo(WholesalerCart::class);
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
     * Obtener el precio mayorista del producto
     */
    public function getWholesalerPriceAttribute(): float
    {
        return $this->current_price * 0.85; // 15% de descuento por defecto
    }

    /**
     * Verificar si el precio ha cambiado
     */
    public function hasPriceChanged(): bool
    {
        return $this->unit_price !== $this->wholesaler_price;
    }

    /**
     * Actualizar precio si ha cambiado
     */
    public function updatePriceIfChanged()
    {
        if ($this->hasPriceChanged()) {
            $this->update([
                'unit_price' => $this->wholesaler_price,
                'total_price' => $this->quantity * $this->wholesaler_price,
                'product_snapshot' => $this->product->toArray(),
            ]);
        }

        return $this;
    }

    /**
     * Calcular descuento aplicado
     */
    public function getDiscountAmountAttribute(): float
    {
        return ($this->current_price - $this->unit_price) * $this->quantity;
    }

    /**
     * Calcular porcentaje de descuento
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->current_price == 0) return 0;
        return (($this->current_price - $this->unit_price) / $this->current_price) * 100;
    }

    /**
     * Verificar si es un item de mayorista
     */
    public function isWholesalerItem(): bool
    {
        return $this->is_wholesaler_item === true;
    }

    /**
     * Agregar notas al item
     */
    public function addNotes($notes)
    {
        $this->update(['notes' => $notes]);
        return $this;
    }

    /**
     * Actualizar cantidad y recalcular total
     */
    public function updateQuantity($quantity)
    {
        $this->update([
            'quantity' => $quantity,
            'total_price' => $quantity * $this->unit_price,
        ]);

        return $this;
    }
}
