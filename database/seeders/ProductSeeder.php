<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronicsCategory = Category::where('name', 'Electrónicos')->first();
        $clothingCategory = Category::where('name', 'Ropa y Accesorios')->first();
        $homeCategory = Category::where('name', 'Hogar y Jardín')->first();

        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'El último iPhone con tecnología avanzada',
                'price' => 999.99,
                'sale_price' => 899.99,
                'sku' => 'IPH15PRO001',
                'stock_quantity' => 50,
                'category_id' => $electronicsCategory->id,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Smartphone Android de última generación',
                'price' => 799.99,
                'sku' => 'SGS24001',
                'stock_quantity' => 30,
                'category_id' => $electronicsCategory->id,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Camiseta Básica',
                'description' => 'Camiseta de algodón 100% orgánico',
                'price' => 29.99,
                'sku' => 'CAM001',
                'stock_quantity' => 100,
                'category_id' => $clothingCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Jeans Clásicos',
                'description' => 'Jeans de corte clásico en color azul',
                'price' => 59.99,
                'sale_price' => 39.99,
                'sku' => 'JEA001',
                'stock_quantity' => 75,
                'category_id' => $clothingCategory->id,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Aspiradora Robot',
                'description' => 'Aspiradora robot inteligente con mapeo',
                'price' => 299.99,
                'sku' => 'ASP001',
                'stock_quantity' => 20,
                'category_id' => $homeCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Laptop ultradelgada con chip M3',
                'price' => 1299.99,
                'sku' => 'MBA001',
                'stock_quantity' => 15,
                'category_id' => $electronicsCategory->id,
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'] ?? null,
                'sku' => $product['sku'],
                'stock_quantity' => $product['stock_quantity'],
                'category_id' => $product['category_id'],
                'is_featured' => $product['is_featured'] ?? false,
                'is_active' => $product['is_active'],
            ]);
        }
    }
}
