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
                'description' => 'Para quienes quieren iniciarse en el mundo cervecero sin complicaciones. Incluye tres cervezas seleccionadas de distintos estilos y países, con propuestas equilibradas y fáciles de disfrutar. Ocasionalmente, la caja puede incluir un licor especial para ampliar la experiencia.',
                'price' => 150000,
                'features' => [
                    '3 cervezas artesanales o importadas de distintos estilos',
                    'Ocasionalmente, un licor del mundo en lugar de una cerveza',
                    'Suscripción mensual por un año',
                    'Cancela cuando quieras'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Maestro Cervecero',
                'slug' => 'master_brewer',
                'description' => 'Para quienes ya tienen afinidad con la cerveza y buscan etiquetas con mayor fuerza y personalidad. Una selección que explora estilos más complejos y distintivos según tu carácter. Según la coyuntura del mes, la caja puede incluir un espirituoso premium para empezar a la experiencia.',
                'price' => 250000,
                'features' => [
                    '5 cervezas artesanales o importadas de distintos estilos',
                    'Ocasionalmente, un licor del mundo en lugar de una cerveza',
                    'Suscripción mensual por un año',
                    'Cancela cuando quieras'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Coleccionista Cervecero',
                'slug' => 'collector_brewer',
                'description' => 'Selección de Élite. La máxima expresión de lo premium y lo inalcanzable. Recibe tres cervezas de la cúspide del mundo cervecero y los destilados. Cada caja es un manifiesto de exclusividad que incluye etiquetas de culto, ediciones especiales y, ocasionalmente, una botella de licor excepcional seleccionada para llevar la experiencia al nivel de lo sublime.',
                'price' => 200000,
                'features' => [
                    '3 cervezas de alta gama o ediciones limitadas',
                    'Algunos meses, un espirituoso excepcional en lugar de una cerveza',
                    'Suscripción mensual por un año',
                    'Cancela cuando quieras'
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
