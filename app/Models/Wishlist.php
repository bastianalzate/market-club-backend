<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Agregar producto a la wishlist
     */
    public static function addProduct($userId, $productId)
    {
        return static::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    /**
     * Remover producto de la wishlist
     */
    public static function removeProduct($userId, $productId)
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Verificar si un producto está en la wishlist
     */
    public static function isInWishlist($userId, $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Obtener wishlist de un usuario
     */
    public static function getUserWishlist($userId)
    {
        return static::where('user_id', $userId)
            ->with(['product.category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}