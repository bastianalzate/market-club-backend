<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Relación con los items del carrito
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Obtener el carrito activo de un usuario
     */
    public static function getActiveCart($userId, $sessionId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return null;
        }

        return $query->with(['items.product'])->first();
    }

    /**
     * Crear o obtener carrito activo
     */
    public static function getOrCreateActiveCart($userId, $sessionId = null)
    {
        $cart = static::getActiveCart($userId, $sessionId);

        if (!$cart) {
            $cart = static::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        }

        return $cart->load(['items.product']);
    }

    /**
     * Calcular totales del carrito
     */
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('total_price');
        $taxRate = 0.19; // 19% IVA
        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = $subtotal > 100000 ? 0 : 10000; // Envío gratis sobre $100,000
        $totalAmount = $subtotal + $taxAmount + $shippingAmount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'total_amount' => $totalAmount,
        ]);

        return $this;
    }

    /**
     * Agregar producto al carrito
     */
    public function addProduct($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        
        $existingItem = $this->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'total_price' => ($existingItem->quantity + $quantity) * $existingItem->unit_price,
            ]);
        } else {
            $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product->current_price,
                'total_price' => $quantity * $product->current_price,
                'product_snapshot' => $product->toArray(),
            ]);
        }

        return $this->calculateTotals();
    }

    /**
     * Actualizar cantidad de un producto
     */
    public function updateProductQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeProduct($productId);
        }

        $item = $this->items()->where('product_id', $productId)->first();
        
        if ($item) {
            $item->update([
                'quantity' => $quantity,
                'total_price' => $quantity * $item->unit_price,
            ]);
        }

        return $this->calculateTotals();
    }

    /**
     * Remover producto del carrito
     */
    public function removeProduct($productId)
    {
        $this->items()->where('product_id', $productId)->delete();
        return $this->calculateTotals();
    }

    /**
     * Limpiar carrito
     */
    public function clear()
    {
        $this->items()->delete();
        return $this->calculateTotals();
    }

    /**
     * Verificar si el carrito está vacío
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Obtener cantidad total de items
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}