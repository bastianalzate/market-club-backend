<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class BeerProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categoría y tipo de producto
        $category = Category::where('name', 'Bebidas Alcohólicas')->first();
        $productType = ProductType::where('slug', 'cervezas')->first();

        if (!$category || !$productType) {
            $this->command->error('Categoría o tipo de producto no encontrado');
            return;
        }

        // Productos de cerveza reales
        $beerProducts = [
            [
                'name' => 'Corona Extra',
                'description' => 'Cerveza lager mexicana, ligera y refrescante con un sabor suave y balanceado.',
                'price' => 8500,
                'stock_quantity' => 50,
                'product_specific_data' => [
                    'country_of_origin' => 'México',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 4.5,
                    'beer_style' => 'lager',
                    'brewery' => 'Grupo Modelo',
                ],
            ],
            [
                'name' => 'Heineken',
                'description' => 'Cerveza lager holandesa premium con un sabor distintivo y refrescante.',
                'price' => 12000,
                'stock_quantity' => 30,
                'product_specific_data' => [
                    'country_of_origin' => 'Países Bajos',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Heineken International',
                ],
            ],
            [
                'name' => 'Stella Artois',
                'description' => 'Cerveza lager belga premium con tradición desde 1366.',
                'price' => 15000,
                'stock_quantity' => 25,
                'product_specific_data' => [
                    'country_of_origin' => 'Bélgica',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Anheuser-Busch InBev',
                ],
            ],
            [
                'name' => 'Budweiser',
                'description' => 'La cerveza de Estados Unidos, conocida como "La Reina de las Cervezas".',
                'price' => 10000,
                'stock_quantity' => 40,
                'product_specific_data' => [
                    'country_of_origin' => 'Estados Unidos',
                    'volume_ml' => '355',
                    'packaging_type' => 'lata',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Anheuser-Busch',
                ],
            ],
            [
                'name' => 'Guinness Draught',
                'description' => 'Cerveza stout irlandesa con sabor robusto y cremoso.',
                'price' => 18000,
                'stock_quantity' => 20,
                'product_specific_data' => [
                    'country_of_origin' => 'Irlanda',
                    'volume_ml' => '500',
                    'packaging_type' => 'lata',
                    'alcohol_content' => 4.2,
                    'beer_style' => 'stout',
                    'brewery' => 'Guinness',
                ],
            ],
            [
                'name' => 'Beck\'s',
                'description' => 'Cerveza lager alemana premium con sabor distintivo.',
                'price' => 13000,
                'stock_quantity' => 35,
                'product_specific_data' => [
                    'country_of_origin' => 'Alemania',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Anheuser-Busch InBev',
                ],
            ],
            [
                'name' => 'Sapporo Premium',
                'description' => 'Cerveza lager japonesa premium con sabor suave y limpio.',
                'price' => 16000,
                'stock_quantity' => 15,
                'product_specific_data' => [
                    'country_of_origin' => 'Japón',
                    'volume_ml' => '330',
                    'packaging_type' => 'lata',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Sapporo Breweries',
                ],
            ],
            [
                'name' => 'Tsingtao',
                'description' => 'Cerveza lager china con sabor suave y refrescante.',
                'price' => 9000,
                'stock_quantity' => 45,
                'product_specific_data' => [
                    'country_of_origin' => 'China',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 4.7,
                    'beer_style' => 'lager',
                    'brewery' => 'Tsingtao Brewery',
                ],
            ],
            [
                'name' => 'Singha',
                'description' => 'Cerveza lager tailandesa premium con sabor distintivo.',
                'price' => 11000,
                'stock_quantity' => 28,
                'product_specific_data' => [
                    'country_of_origin' => 'Tailandia',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Boon Rawd Brewery',
                ],
            ],
            [
                'name' => 'Estrella Damm',
                'description' => 'Cerveza lager española con sabor mediterráneo.',
                'price' => 14000,
                'stock_quantity' => 22,
                'product_specific_data' => [
                    'country_of_origin' => 'España',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.4,
                    'beer_style' => 'lager',
                    'brewery' => 'Damm',
                ],
            ],
            [
                'name' => 'Cusqueña Premium',
                'description' => 'Cerveza lager peruana premium con sabor andino.',
                'price' => 12000,
                'stock_quantity' => 32,
                'product_specific_data' => [
                    'country_of_origin' => 'Perú',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Backus y Johnston',
                ],
            ],
            [
                'name' => 'Dos Equis XX Lager',
                'description' => 'Cerveza lager mexicana con sabor distintivo y balanceado.',
                'price' => 9500,
                'stock_quantity' => 38,
                'product_specific_data' => [
                    'country_of_origin' => 'México',
                    'volume_ml' => '355',
                    'packaging_type' => 'lata',
                    'alcohol_content' => 4.2,
                    'beer_style' => 'lager',
                    'brewery' => 'Cuauhtémoc Moctezuma',
                ],
            ],
            [
                'name' => 'Pilsner Urquell',
                'description' => 'La cerveza pilsner original de República Checa.',
                'price' => 17000,
                'stock_quantity' => 18,
                'product_specific_data' => [
                    'country_of_origin' => 'República Checa',
                    'volume_ml' => '500',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 4.4,
                    'beer_style' => 'pilsner',
                    'brewery' => 'Plzeňský Prazdroj',
                ],
            ],
            [
                'name' => 'Carlsberg',
                'description' => 'Cerveza lager danesa con sabor suave y refrescante.',
                'price' => 13000,
                'stock_quantity' => 26,
                'product_specific_data' => [
                    'country_of_origin' => 'Dinamarca',
                    'volume_ml' => '330',
                    'packaging_type' => 'botella',
                    'alcohol_content' => 5.0,
                    'beer_style' => 'lager',
                    'brewery' => 'Carlsberg Group',
                ],
            ],
            [
                'name' => 'Miller Lite',
                'description' => 'Cerveza lager americana ligera y refrescante.',
                'price' => 8500,
                'stock_quantity' => 42,
                'product_specific_data' => [
                    'country_of_origin' => 'Estados Unidos',
                    'volume_ml' => '355',
                    'packaging_type' => 'lata',
                    'alcohol_content' => 4.2,
                    'beer_style' => 'lager',
                    'brewery' => 'MillerCoors',
                ],
            ],
        ];

        // Crear productos
        foreach ($beerProducts as $productData) {
            $slug = \Illuminate\Support\Str::slug($productData['name']);
            Product::create([
                'name' => $productData['name'],
                'slug' => $slug,
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock_quantity' => $productData['stock_quantity'],
                'category_id' => $category->id,
                'product_type_id' => $productType->id,
                'product_specific_data' => $productData['product_specific_data'],
                'is_active' => true,
                'is_featured' => rand(0, 1) == 1, // Algunos productos destacados
                'sku' => 'BEER-' . strtoupper(substr(md5($productData['name']), 0, 8)),
            ]);
        }

        $this->command->info('Productos de cerveza creados exitosamente: ' . count($beerProducts) . ' productos');
    }
}