<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Console\Command;

class NormalizeBeerStyles extends Command
{
    protected $signature = 'beer-styles:normalize {--dry-run : Mostrar cambios sin guardarlos}';

    protected $description = 'Normalizar los valores de beer_style existentes al nuevo catálogo oficial';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $this->info(($dryRun ? '[DRY RUN] ' : '') . 'Iniciando normalización de estilos de cerveza...');

        $processed = 0;
        $updated = 0;
        $cleared = 0;

        Product::whereNotNull('product_specific_data')
            ->orderBy('id')
            ->chunkById(200, function ($products) use (&$processed, &$updated, &$cleared) {
                foreach ($products as $product) {
                    $processed++;

                    $data = $product->product_specific_data ?? [];

                    if (!isset($data['beer_style'])) {
                        continue;
                    }

                    $original = $data['beer_style'];
                    $normalized = ProductType::normalizeBeerStyle($original);

                    if ($normalized === null) {
                        unset($data['beer_style']);
                        $cleared++;
                    } elseif ($normalized !== $original) {
                        $data['beer_style'] = $normalized;
                        $updated++;
                    } else {
                        continue;
                    }

                    $product->product_specific_data = $data;

                    if (!$this->option('dry-run')) {
                        $product->saveQuietly();
                    }

                    $this->line(sprintf(
                        'Producto #%d (%s): %s -> %s',
                        $product->id,
                        $product->name,
                        $original ?? 'null',
                        $normalized ?? 'null'
                    ));
                }
            });

        $this->newLine();
        $this->info("Productos revisados: {$processed}");
        $this->info("Estilos actualizados: {$updated}");
        $this->info("Estilos eliminados por estar vacíos o inválidos: {$cleared}");

        if ($dryRun) {
            $this->comment('No se guardaron cambios (modo dry-run).');
        }

        $this->info('Normalización completada.');

        return Command::SUCCESS;
    }
}

