<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electrónicos',
                'description' => 'Dispositivos electrónicos y tecnología',
                'is_active' => true,
            ],
            [
                'name' => 'Ropa y Accesorios',
                'description' => 'Ropa, calzado y accesorios de moda',
                'is_active' => true,
            ],
            [
                'name' => 'Hogar y Jardín',
                'description' => 'Artículos para el hogar y jardín',
                'is_active' => true,
            ],
            [
                'name' => 'Deportes',
                'description' => 'Equipos y accesorios deportivos',
                'is_active' => true,
            ],
            [
                'name' => 'Libros',
                'description' => 'Libros y material educativo',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => $category['is_active'],
            ]);
        }
    }
}
