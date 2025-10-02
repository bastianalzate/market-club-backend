<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wholesaler;

class WholesalerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wholesalers = [
            [
                'business_name' => 'Restaurante El Buen Sabor',
                'contact_name' => 'María González',
                'email' => 'maria.gonzalez@elbuensabor.com',
                'phone' => '+57 300 123 4567',
                'tax_id' => '900123456-1',
                'address' => 'Calle 80 #45-67',
                'city' => 'Bogotá',
                'state' => 'Cundinamarca',
                'country' => 'Colombia',
                'postal_code' => '110221',
                'business_type' => 'restaurant',
                'business_description' => 'Restaurante familiar especializado en comida colombiana tradicional',
                    'status' => 'enabled',
                    'notes' => 'Cliente preferencial, excelente historial de pagos',
            ],
            [
                'business_name' => 'Bar La Cervecería',
                'contact_name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@lacerveceria.com',
                'phone' => '+57 310 987 6543',
                'tax_id' => '800987654-2',
                'address' => 'Carrera 15 #93-47',
                'city' => 'Medellín',
                'state' => 'Antioquia',
                'country' => 'Colombia',
                'postal_code' => '050031',
                'business_type' => 'bar',
                'business_description' => 'Bar especializado en cervezas artesanales nacionales e internacionales',
                'status' => 'enabled',
                'notes' => 'Pagos puntuales, amplia variedad de productos',
            ],
            [
                'business_name' => 'Tienda Gourmet Deluxe',
                'contact_name' => 'Ana Martínez',
                'email' => 'ana.martinez@gourmetdeluxe.com',
                'phone' => '+57 315 456 7890',
                'tax_id' => '900456789-3',
                'address' => 'Avenida 68 #25-47',
                'city' => 'Cali',
                'state' => 'Valle del Cauca',
                'country' => 'Colombia',
                'postal_code' => '760001',
                'business_type' => 'retail_store',
                'business_description' => 'Tienda especializada en productos gourmet y bebidas premium',
                'status' => 'disabled',
                'notes' => 'Nueva solicitud, requiere verificación de documentos',
            ],
            [
                'business_name' => 'Distribuidora Nacional de Bebidas',
                'contact_name' => 'Roberto Silva',
                'email' => 'roberto.silva@distribuidoranacional.com',
                'phone' => '+57 320 555 1234',
                'tax_id' => '800555123-4',
                'address' => 'Zona Industrial, Calle 100 #15-30',
                'city' => 'Barranquilla',
                'state' => 'Atlántico',
                'country' => 'Colombia',
                'postal_code' => '080001',
                'business_type' => 'distributor',
                'business_description' => 'Distribuidora mayorista de bebidas alcohólicas y no alcohólicas',
                'status' => 'enabled',
                'notes' => 'Cliente corporativo, volumen alto de compras',
            ],
            [
                'business_name' => 'Café & Más',
                'contact_name' => 'Laura Jiménez',
                'email' => 'laura.jimenez@cafeymas.com',
                'phone' => '+57 318 777 8888',
                'tax_id' => '900777888-5',
                'address' => 'Calle 50 #22-15',
                'city' => 'Pereira',
                'state' => 'Risaralda',
                'country' => 'Colombia',
                'postal_code' => '660001',
                'business_type' => 'other',
                'business_description' => 'Café especializado con venta de bebidas y productos gourmet',
                'status' => 'disabled',
                'notes' => 'Cliente suspendido temporalmente por retrasos en pagos',
            ],
        ];

        foreach ($wholesalers as $wholesalerData) {
            Wholesaler::create($wholesalerData);
        }
    }
}
