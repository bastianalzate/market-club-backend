<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipo de producto "Cervezas"
        ProductType::firstOrCreate(
            ['slug' => 'cervezas'],
            [
                'name' => 'Cervezas',
                'description' => 'Productos de cerveza con características específicas como país de origen, tamaño, tipo de envase, etc.',
                'fields_config' => ProductType::getBeerFieldsConfig(),
                'is_active' => true,
            ]
        );


        $this->command->info('Tipo de producto "Cervezas" creado exitosamente.');
    }
}