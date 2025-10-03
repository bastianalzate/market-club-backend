<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportBeerProducts extends Command
{
    protected $signature = 'import:beer-products';
    protected $description = 'Import beer products from Excel file';

    public function handle()
    {
        $this->info('Iniciando importación de productos de cerveza...');

        // Limpiar productos existentes
        $this->info('Eliminando productos existentes...');
        Product::query()->delete();

        // Obtener categoría y tipo de producto
        $category = Category::where('name', 'Bebidas Alcohólicas')->first();
        $productType = ProductType::where('slug', 'cervezas')->first();

        if (!$category) {
            $this->error('Categoría "Bebidas Alcohólicas" no encontrada');
            return;
        }

        if (!$productType) {
            $this->error('Tipo de producto "Cervezas" no encontrado');
            return;
        }

        // Leer archivo Excel
        $filePath = base_path('data/INVENTARIO CERVEZAS Y ESTUCHES MARKET.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error('Archivo Excel no encontrado en: ' . $filePath);
            return;
        }

        try {
            $data = Excel::load($filePath)->get();
            $this->info('Archivo Excel leído exitosamente. Filas encontradas: ' . $data->count());

            $imported = 0;
            $errors = 0;

            foreach ($data as $row) {
                try {
                    // Mapear datos del Excel a campos del producto
                    $productData = $this->mapExcelDataToProduct($row, $category->id, $productType->id);
                    
                    if ($productData) {
                        Product::create($productData);
                        $imported++;
                        $this->line("✓ Producto creado: " . $productData['name']);
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("✗ Error en fila: " . $e->getMessage());
                }
            }

            $this->info("Importación completada:");
            $this->info("- Productos importados: {$imported}");
            $this->info("- Errores: {$errors}");

        } catch (\Exception $e) {
            $this->error('Error al leer el archivo Excel: ' . $e->getMessage());
        }
    }

    private function mapExcelDataToProduct($row, $categoryId, $productTypeId)
    {
        // Mapear columnas del Excel a campos del producto
        // Ajustar estos nombres según las columnas reales del Excel
        
        $name = $row->get('nombre') ?? $row->get('producto') ?? $row->get('descripcion') ?? 'Producto sin nombre';
        $description = $row->get('descripcion') ?? $row->get('detalle') ?? 'Descripción no disponible';
        $price = $this->parsePrice($row->get('precio') ?? $row->get('valor') ?? 0);
        $stock = $this->parseStock($row->get('stock') ?? $row->get('cantidad') ?? 0);
        
        // Datos específicos de cerveza
        $country = $this->mapCountry($row->get('pais') ?? $row->get('origen') ?? 'Colombia');
        $size = $this->parseSize($row->get('tamaño') ?? $row->get('volumen') ?? $row->get('ml') ?? '330');
        $container = $this->mapContainer($row->get('envase') ?? $row->get('presentacion') ?? 'botella');
        $alcohol = $this->parseAlcohol($row->get('alcohol') ?? $row->get('graduacion') ?? 0);
        $style = $row->get('estilo') ?? $row->get('tipo') ?? '';
        $brewery = $row->get('cerveceria') ?? $row->get('marca') ?? '';

        $productSpecificData = [
            'country_of_origin' => $country,
            'volume_ml' => $size,
            'packaging_type' => $container,
            'alcohol_content' => $alcohol,
            'beer_style' => $style,
            'brewery' => $brewery,
        ];

        return [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock_quantity' => $stock,
            'category_id' => $categoryId,
            'product_type_id' => $productTypeId,
            'product_specific_data' => $productSpecificData,
            'is_active' => true,
            'is_featured' => false,
            'sku' => 'BEER-' . strtoupper(substr(md5($name), 0, 8)),
        ];
    }

    private function parsePrice($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Remover símbolos de moneda y espacios
        $clean = preg_replace('/[^\d.,]/', '', $value);
        $clean = str_replace(',', '.', $clean);
        
        return is_numeric($clean) ? (float) $clean : 0;
    }

    private function parseStock($value)
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function mapCountry($country)
    {
        $countries = [
            'Alemania' => 'Alemania',
            'Bélgica' => 'Bélgica',
            'España' => 'España',
            'China' => 'China',
            'Japón' => 'Japón',
            'Holanda' => 'Países Bajos',
            'Escocia' => 'Inglaterra',
            'Reino Unido' => 'Inglaterra',
            'Tailandia' => 'Tailandia',
            'Mexico' => 'México',
            'Peru' => 'Perú',
        ];

        $country = trim($country);
        return $countries[$country] ?? 'Colombia';
    }

    private function parseSize($value)
    {
        // Extraer números del valor
        preg_match('/(\d+)/', $value, $matches);
        $size = isset($matches[1]) ? (int) $matches[1] : 330;
        
        // Validar tamaños permitidos
        $validSizes = [250, 330, 355, 500, 650, 750, 1000];
        return in_array($size, $validSizes) ? (string) $size : '330';
    }

    private function mapContainer($value)
    {
        $containers = [
            'botella' => 'botella',
            'lata' => 'lata',
            'barril' => 'barril',
            'growler' => 'growler',
        ];

        $value = strtolower(trim($value));
        return $containers[$value] ?? 'botella';
    }

    private function parseAlcohol($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Extraer números del valor
        preg_match('/(\d+(?:\.\d+)?)/', $value, $matches);
        return isset($matches[1]) ? (float) $matches[1] : 0;
    }
}