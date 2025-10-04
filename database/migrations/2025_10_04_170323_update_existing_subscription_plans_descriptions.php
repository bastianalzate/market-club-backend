<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar descripción de Curioso Cervecero
        DB::table('subscription_plans')
            ->where('slug', 'curious_brewer')
            ->update([
                'description' => 'Para quienes quieren iniciarse en el mundo cervecero sin complicaciones. Incluye tres cervezas seleccionadas de distintos estilos y países, con propuestas equilibradas y fáciles de disfrutar. Ocasionalmente, la caja puede incluir un licor especial para ampliar la experiencia.',
                'updated_at' => now()
            ]);

        // Actualizar descripción de Maestro Cervecero (Coleccionista Cervecero)
        DB::table('subscription_plans')
            ->where('slug', 'master_brewer')
            ->update([
                'description' => 'Selección de Élite. La máxima expresión de lo premium y lo inalcanzable. Recibe tres cervezas de la cúspide del mundo cervecero y los destilados. Cada caja es un manifiesto de exclusividad que incluye etiquetas de culto, ediciones especiales y, ocasionalmente, una botella de licor excepcional seleccionada para llevar la experiencia al nivel de lo sublime.',
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir descripción de Curioso Cervecero
        DB::table('subscription_plans')
            ->where('slug', 'curious_brewer')
            ->update([
                'description' => 'Para quienes quieren iniciarse en el mundo cervecero sin complicaciones. Incluye tres cervezas seleccionadas de distintos estilos y países, con propuestas equilibradas y fáciles de disfrutar. Ocasionalmente, la caja puede incluir un licor especial para ampliar la experiencia.',
                'updated_at' => now()
            ]);

        // Revertir descripción de Maestro Cervecero
        DB::table('subscription_plans')
            ->where('slug', 'master_brewer')
            ->update([
                'description' => 'Selección de Élite. La máxima expresión de lo premium y lo inalcanzable. Recibe tres cervezas de la cúspide del mundo cervecero y los destilados. Cada caja es un manifiesto de exclusividad que incluye etiquetas de culto, ediciones especiales y, ocasionalmente, una botella de licor excepcional seleccionada para llevar la experiencia al nivel de lo sublime.',
                'updated_at' => now()
            ]);
    }
};
