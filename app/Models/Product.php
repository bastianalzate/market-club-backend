<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'image',
        'gallery',
        'is_active',
        'is_featured',
        'category_id',
        'product_type_id',
        'attributes',
        'product_specific_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'gallery' => 'array',
        'attributes' => 'array',
        'product_specific_data' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Si la ruta ya incluye 'uploads/', usarla directamente
        if (str_starts_with($this->image, 'uploads/')) {
            return asset($this->image);
        }
        
        // Si la ruta incluye 'products/', asumimos que está en storage/app/public
        if (str_starts_with($this->image, 'products/')) {
            // Verificar si el archivo existe en storage/app/public
            if (Storage::disk('public')->exists($this->image)) {
                return asset('storage/' . $this->image);
            }
        }
        
        // Compatibilidad con rutas antiguas
        return asset('storage/' . $this->image);
    }
}
