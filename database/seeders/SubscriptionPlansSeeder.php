<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Curioso Cervecero',
                'slug' => 'curious_brewer',
                'description' => 'Para quienes quieren iniciar en el mundo cervecero sin complicaciones. Tres cervezas seleccionadas de distintos estilos y países, siempre con propuestas equilibradas y fáciles de disfrutar. Algunas veces, la caja puede incluir un licor especial para ampliar la experiencia.',
                'price' => 150000,
                'features' => [
                    'Descuentos del 10% en todas las cervezas',
                    'Envío gratis en compras superiores a $100.000',
                    'Acceso a catálogo básico de cervezas nacionales',
                    'Newsletter mensual con novedades cerveceras',
                    'Soporte al cliente por WhatsApp',
                    'Acceso a eventos públicos de degustación',
                    'Historial de compras y recomendaciones básicas',
                    'Puntos de fidelidad por cada compra'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Coleccionista Cervecero',
                'slug' => 'collector_brewer',
                'description' => 'Para quienes ya tienen afinidad con la cerveza y buscan etiquetas con mayor fuerza y personalidad. Una selección que explora estilos más complejos y distintivos según tu carácter. Según la coyuntura del mes, la caja puede incluir un espirituoso premium para empezar a la experiencia.',
                'price' => 200000,
                'features' => [
                    'Invitaciones a catas privadas, degustaciones y eventos exclusivos',
                    'Acceso anticipado con precios preferenciales a nuevas colecciones y lanzamientos',
                    'Prioridad en reservas para cenas, experiencias gastronómicas y eventos especiales',
                    'Eventos exclusivos para regular experiencias gastronómicas por temporadas',
                    'Cupones descuentos para regular experiencia en fechas especiales (cumpleaños, aniversarios)'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Maestro Cervecero',
                'slug' => 'master_brewer',
                'description' => 'Selección sin límites. Tres cervezas con lo mejor del mundo cervecero y los destilados, ediciones especiales, etiquetas de culto y, a veces, un licor experimental de encontrar.',
                'price' => 200000,
                'features' => [
                    'Atención personalizada por WhatsApp para recibir recomendaciones y ofertas',
                    'Personaliza tu caja mensual siguiendo tus gustos y preferencias',
                    'Armar un catálogo personal con precio preferencial',
                    'Recomendaciones inmediatas y alertas sobre lanzamientos y novedades'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
