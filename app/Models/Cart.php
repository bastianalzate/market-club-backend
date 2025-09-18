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
        // Cargar items si no están cargados
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        
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
     * Agregar regalo personalizado al carrito
     */
    public function addGift($giftId, $quantity = 1, $giftData = [])
    {
        // Verificar si ya existe un item con este ID de regalo
        $existingItem = $this->items()->where('product_id', null)
            ->where('gift_id', $giftId)
            ->first();

        $totalPrice = $giftData['totalPrice'] ?? 0;
        $unitPrice = $totalPrice; // Para regalos, el precio unitario es el total

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'total_price' => ($existingItem->quantity + $quantity) * $unitPrice,
            ]);
        } else {
            $this->items()->create([
                'product_id' => null, // Null para productos especiales
                'gift_id' => $giftId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
                'product_snapshot' => null,
                'gift_data' => $giftData,
                'is_gift' => true,
            ]);
        }

        // Recargar los items antes de calcular totales
        $this->load('items');
        $this->calculateTotals();
        
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
     * Actualizar cantidad de un regalo
     */
    public function updateGiftQuantity($giftId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeGift($giftId);
        }

        $item = $this->items()->where('product_id', null)
            ->where('gift_id', $giftId)
            ->first();
        
        if ($item) {
            $item->update([
                'quantity' => $quantity,
                'total_price' => $quantity * $item->unit_price,
            ]);
        }

        // Recargar los items antes de calcular totales
        $this->load('items');
        $this->calculateTotals();
        
        return $this;
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
     * Remover regalo del carrito
     */
    public function removeGift($giftId)
    {
        $this->items()->where('product_id', null)
            ->where('gift_id', $giftId)
            ->delete();
        
        // Recargar los items antes de calcular totales
        $this->load('items');
        $this->calculateTotals();
        
        return $this;
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