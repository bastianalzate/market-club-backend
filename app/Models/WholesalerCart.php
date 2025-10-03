<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WholesalerCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'wholesaler_id',
        'session_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'wholesaler_id' => 'integer',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Relación con el mayorista
     */
    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(Wholesaler::class);
    }

    /**
     * Relación con los items del carrito
     */
    public function items(): HasMany
    {
        return $this->hasMany(WholesalerCartItem::class);
    }

    /**
     * Obtener el carrito activo de un mayorista
     */
    public static function getActiveCart($wholesalerId, $sessionId = null)
    {
        $query = static::query();
        
        if ($wholesalerId) {
            $query->where('wholesaler_id', $wholesalerId);
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
    public static function getOrCreateActiveCart($wholesalerId, $sessionId = null)
    {
        $cart = static::getActiveCart($wholesalerId, $sessionId);

        if (!$cart) {
            $cart = static::create([
                'wholesaler_id' => $wholesalerId,
                'session_id' => $sessionId,
            ]);
        }

        return $cart->load(['items.product']);
    }

    /**
     * Calcular totales del carrito con precios especiales para mayoristas
     */
    public function calculateTotals()
    {
        // Cargar items si no están cargados
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        
        $subtotal = $this->items->sum('total_price');
        
        // IVA reducido para mayoristas (5% en lugar de 19%)
        $taxRate = 0.05;
        $taxAmount = $subtotal * $taxRate;
        
        // Envío gratis para pedidos mayores a $500,000 (mayoristas)
        $shippingAmount = $subtotal > 500000 ? 0 : 15000;
        
        // Descuento por volumen (5% si el subtotal es mayor a $1,000,000)
        $discountAmount = 0;
        if ($subtotal > 1000000) {
            $discountAmount = $subtotal * 0.05;
        }
        
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);

        return $this;
    }

    /**
     * Agregar producto al carrito con precios mayoristas
     */
    public function addProduct($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        
        // Verificar que el mayorista esté habilitado
        if ($this->wholesaler_id && !$this->wholesaler->isEnabled()) {
            throw new \Exception('El mayorista no está habilitado para realizar compras');
        }
        
        $existingItem = $this->items()->where('product_id', $productId)->first();

        // Precio especial para mayoristas (descuento del 15% por defecto)
        $wholesalerPrice = $product->current_price * 0.85;

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'total_price' => ($existingItem->quantity + $quantity) * $wholesalerPrice,
            ]);
        } else {
            // Crear snapshot completo del producto incluyendo campos calculados
            $productSnapshot = $product->toArray();
            $productSnapshot['current_price'] = $product->current_price;
            $productSnapshot['discount_percentage'] = $product->discount_percentage;
            
            $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $wholesalerPrice,
                'total_price' => $quantity * $wholesalerPrice,
                'product_snapshot' => $productSnapshot,
                'is_wholesaler_item' => true,
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

    /**
     * Aplicar descuento especial
     */
    public function applyDiscount($discountAmount, $reason = null)
    {
        $this->update([
            'discount_amount' => $discountAmount,
            'metadata' => array_merge($this->metadata ?? [], [
                'discount_reason' => $reason,
                'discount_applied_at' => now()->toISOString(),
            ])
        ]);

        return $this->calculateTotals();
    }

    /**
     * Agregar notas al carrito
     */
    public function addNotes($notes)
    {
        $this->update(['notes' => $notes]);
        return $this;
    }
}
