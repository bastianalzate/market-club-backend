<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class RealBeerProductSeeder extends Seeder
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

        // Datos reales del Excel
        $realBeerProducts = [
            ['name' => 'ADNAMS GHOST SHIP', 'country' => 'Inglaterra', 'price' => 29000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'ADNAMS KOBOLD ENGLISH LAGER', 'country' => 'Inglaterra', 'price' => 29000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'AGUILA ORIGINAL LATA X 330ML', 'country' => 'Colombia', 'price' => 4500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'ANDINA DORADA', 'country' => 'Colombia', 'price' => 4000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'ARTESANAL BLONDE ALE', 'country' => 'Colombia', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'ARTESANAL PORTER', 'country' => 'Colombia', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'ARTESANAL SAISON', 'country' => 'Colombia', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BARRIL BITBURGER 5LTR', 'country' => 'Alemania', 'price' => 148000, 'size' => '5000', 'container' => 'barril'],
            ['name' => 'BENEDIK TINER BARRILITO 5 LTRS', 'country' => 'Alemania', 'price' => 154000, 'size' => '5000', 'container' => 'barril'],
            ['name' => 'BENEDIKTINER LATA 500ML', 'country' => 'Alemania', 'price' => 13000, 'size' => '500', 'container' => 'lata'],
            ['name' => 'BIRRA PERONI BOTELLA 330', 'country' => 'Italia', 'price' => 9500, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BITBURGER DRIVE 0.0%', 'country' => 'Alemania', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BITBURGER PREMIUM PILS', 'country' => 'Alemania', 'price' => 14000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BITBURGER PREMIUM X 500 ML', 'country' => 'Alemania', 'price' => 18800, 'size' => '500', 'container' => 'botella'],
            ['name' => 'BITBURGUER LATA + 13,5 FREE', 'country' => 'Alemania', 'price' => 16000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'BREWDOG ELVIS JUICE LATA', 'country' => 'Escocia', 'price' => 16500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'BREWDOG HAZY JANE', 'country' => 'Escocia', 'price' => 17000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BREWDOG HAZY JANE LATA 330ML', 'country' => 'Escocia', 'price' => 15000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'BRUDER MANGO GRANADILLA', 'country' => 'Colombia', 'price' => 12000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'BRUGSE ZOT BLOND 330', 'country' => 'Bélgica', 'price' => 26000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVERZA HOFBRAU MUNCHEN BOTELLA X 500 ML', 'country' => 'Alemania', 'price' => 18800, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA 1906', 'country' => 'España', 'price' => 17000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA 8.6 BLACK DARK BEER X 500 ML', 'country' => 'Países Bajos', 'price' => 19000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA 8.6 EXTREME STRONG BEER X 500 ML', 'country' => 'Países Bajos', 'price' => 19000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA 8.6 ORIGINAL BLOND BEER X 500 ML', 'country' => 'Países Bajos', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA 8.6 RED RED BEER X 500 ML', 'country' => 'Países Bajos', 'price' => 21000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA ARTESANAL IPA', 'country' => 'Colombia', 'price' => 13600, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA ASAHI SUPER DRY BOTELLA 330', 'country' => 'Japón', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA BBC UNIDAD', 'country' => 'Colombia', 'price' => 5000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA CORONA EXTRA 330 ML', 'country' => 'México', 'price' => 7000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA COSTEÑA LATA X 330 ML', 'country' => 'Colombia', 'price' => 4000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'CERVEZA CUSQUEÑA', 'country' => 'Perú', 'price' => 8500, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA CZECHVAR CZECH LAGER X 330 ML', 'country' => 'República Checa', 'price' => 13000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA DUNKEL ERDINGER X 500 ML', 'country' => 'Alemania', 'price' => 23000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA DUVEL BELGIAN GOLDEN ALE X 330 ML', 'country' => 'Bélgica', 'price' => 26000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA ERDINGER WEIBBIER', 'country' => 'Alemania', 'price' => 22000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA ESTRELLA GALICIA  XUND', 'country' => 'España', 'price' => 17000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA ESTRELLA GALICIA LATA', 'country' => 'España', 'price' => 18000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'CERVEZA GERMAN RED STEAM BREW X 500 ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA GULDEN DRAAK X UND', 'country' => 'Bélgica', 'price' => 34000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA HOLLANDIA PREMIUM', 'country' => 'Países Bajos', 'price' => 14000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA IRON MAIDEN X 500 ML', 'country' => 'Inglaterra', 'price' => 22000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA LA CHOUFFE 40 X UND', 'country' => 'Bélgica', 'price' => 25000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA LIEFMANS FRUITESSE BOTELLA X 250 ML', 'country' => 'Bélgica', 'price' => 20000, 'size' => '250', 'container' => 'botella'],
            ['name' => 'CERVEZA MAHOU', 'country' => 'España', 'price' => 9000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA MODELO ESPECIAL', 'country' => 'México', 'price' => 9000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA PALE ALE STEAM BREW X 500 ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA PILSEN LATA X 330', 'country' => 'Colombia', 'price' => 4500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'CERVEZA POKER LATA X 330 ML', 'country' => 'Colombia', 'price' => 4000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'CERVEZA REDDS LATA X 269 ML', 'country' => 'Colombia', 'price' => 4800, 'size' => '269', 'container' => 'lata'],
            ['name' => 'CERVEZA SOL BNR 330', 'country' => 'México', 'price' => 5000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA STEAM BREW 500ML XUND IMPERIAL IPA', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA STEAM BREW X 500 ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CERVEZA STELLA ARTOIS', 'country' => 'Bélgica', 'price' => 7000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA TEKATE', 'country' => 'México', 'price' => 4000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'CERVEZA WEIDMANN X 500 ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'CLUB COLOMBIA DORADA LATA X 330 ML', 'country' => 'Colombia', 'price' => 5000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'DAB LATA', 'country' => 'Alemania', 'price' => 8500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'DELIRIUM ARGENTUM', 'country' => 'Bélgica', 'price' => 30000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'DELIRIUM CHRISTMAS', 'country' => 'Bélgica', 'price' => 31000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'DELIRIUM RED', 'country' => 'Bélgica', 'price' => 33000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'DELIRIUM TREMEMS BOTELLA NR 330ML X UND', 'country' => 'Bélgica', 'price' => 31000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'EICHBAUM DUNKEL LATA', 'country' => 'Alemania', 'price' => 15000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'ERDINGER ALKOHOL FREE BOT 500ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'ERDINGER URWEISSE BOT 500', 'country' => 'Alemania', 'price' => 22500, 'size' => '500', 'container' => 'botella'],
            ['name' => 'ERDONGER PIKANTUS BOTELLA NR 500ML X UND', 'country' => 'Alemania', 'price' => 24000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'FLENSBURGER DUNKEL', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'FLENSBURGER PILSENER', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'FLENSBURGER WEIZEN', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'FLORIS FRAMBOISE BOTELLA  NR 330ML X UND', 'country' => 'Bélgica', 'price' => 28500, 'size' => '330', 'container' => 'botella'],
            ['name' => 'GERMÁN BEBER AC DC', 'country' => 'Alemania', 'price' => 20000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'GOLD INTENSE BEER', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'HB HOFBRAU DUNKEL', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'HEINEKEN X 330 BOTELLA VIDRIO', 'country' => 'Países Bajos', 'price' => 5000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'INDICA IPA COAST BOT X 355', 'country' => 'Estados Unidos', 'price' => 25000, 'size' => '355', 'container' => 'botella'],
            ['name' => 'INNIS AND GUNN ORIGINAL', 'country' => 'Escocia', 'price' => 21000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'KARLSBRAU LAGER', 'country' => 'Alemania', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'KARLSBRAU URPILS', 'country' => 'Alemania', 'price' => 10000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'KOSTRITZER BARRILITO 5LTRS', 'country' => 'Alemania', 'price' => 160000, 'size' => '5000', 'container' => 'barril'],
            ['name' => 'KOSTRITZER LATA 500ML', 'country' => 'Alemania', 'price' => 14000, 'size' => '500', 'container' => 'lata'],
            ['name' => 'KRISTOFFEL BLOND', 'country' => 'Bélgica', 'price' => 21000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'KRISTOFFEL ROSE', 'country' => 'Bélgica', 'price' => 23000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'KRISTOFFEL WHITE', 'country' => 'Bélgica', 'price' => 21000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'LAGER BEER SHINGHA LATA X 330ML', 'country' => 'Tailandia', 'price' => 11500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'LAGER BEER SINGHA BOTELLA X330ML', 'country' => 'Tailandia', 'price' => 12700, 'size' => '330', 'container' => 'botella'],
            ['name' => 'MILLER LITE LATA X310', 'country' => 'Estados Unidos', 'price' => 4000, 'size' => '310', 'container' => 'lata'],
            ['name' => 'PAULANER WEISSBIER LATA  500  ML', 'country' => 'Alemania', 'price' => 25000, 'size' => '500', 'container' => 'lata'],
            ['name' => 'PILSEN LATON', 'country' => 'Colombia', 'price' => 5000, 'size' => '330', 'container' => 'lata'],
            ['name' => 'REEPER B.WEISSBIER LATA', 'country' => 'Alemania', 'price' => 18500, 'size' => '330', 'container' => 'lata'],
            ['name' => 'SAPPORO PREMIUM BEER', 'country' => 'Japón', 'price' => 15900, 'size' => '330', 'container' => 'botella'],
            ['name' => 'SCHOFFERHOFER', 'country' => 'Alemania', 'price' => 24000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'SCHOFFERHOFER TORONJA GRAPEFRUIT', 'country' => 'Alemania', 'price' => 19000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'ST.IDESBALD  BOT 330', 'country' => 'Bélgica', 'price' => 22000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'STRAFFE HENDRIK TRIPEL X 330', 'country' => 'Bélgica', 'price' => 26000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'TANGERINE LOST COAST', 'country' => 'Estados Unidos', 'price' => 25000, 'size' => '330', 'container' => 'botella'],
            ['name' => 'TRES CORDILLERAS VIDRIO X300ML', 'country' => 'Colombia', 'price' => 5000, 'size' => '300', 'container' => 'botella'],
            ['name' => 'TROOPER FEAR OF THE DARK LATA 500 ML', 'country' => 'Inglaterra', 'price' => 19000, 'size' => '500', 'container' => 'lata'],
            ['name' => 'TROOPER IPA BOTELLA 500  ML', 'country' => 'Inglaterra', 'price' => 27000, 'size' => '500', 'container' => 'botella'],
            ['name' => 'WHEAT PALE ALE STEAM BREW X 500 ML', 'country' => 'Alemania', 'price' => 18000, 'size' => '500', 'container' => 'botella'],
        ];

        // Crear productos
        $created = 0;
        foreach ($realBeerProducts as $productData) {
            $slug = \Illuminate\Support\Str::slug($productData['name']);
            $description = $this->generateDescription($productData);
            $alcohol = $this->getAlcoholContent($productData['name']);
            $style = $this->getBeerStyle($productData['name']);
            $brewery = $this->getBrewery($productData['name']);

            Product::create([
                'name' => $productData['name'],
                'slug' => $slug,
                'description' => $description,
                'price' => $productData['price'],
                'stock_quantity' => rand(10, 100), // Stock aleatorio
                'category_id' => $category->id,
                'product_type_id' => $productType->id,
                'product_specific_data' => [
                    'country_of_origin' => $this->mapCountry($productData['country']),
                    'volume_ml' => $productData['size'],
                    'packaging_type' => $productData['container'],
                    'alcohol_content' => $alcohol,
                    'beer_style' => $style,
                    'brewery' => $brewery,
                ],
                'is_active' => true,
                'is_featured' => $productData['price'] > 20000, // Productos caros son destacados
                'sku' => 'BEER-' . strtoupper(substr(md5($productData['name']), 0, 8)),
            ]);
            $created++;
        }

        $this->command->info("Productos de cerveza reales creados exitosamente: {$created} productos");
    }

    private function generateDescription($productData)
    {
        $country = $productData['country'];
        $size = $productData['size'];
        $container = $productData['container'];
        
        $containerText = $container === 'barril' ? 'barril de' : ($container === 'lata' ? 'lata de' : 'botella de');
        
        return "Cerveza {$country} en {$containerText} {$size}ml. Producto importado de alta calidad.";
    }

    private function getAlcoholContent($name)
    {
        // Detectar contenido de alcohol basado en el nombre
        if (strpos($name, '0.0%') !== false || strpos($name, 'ALKOHOL FREE') !== false) {
            return 0.0;
        }
        if (strpos($name, '8.6') !== false) {
            return 8.6;
        }
        if (strpos($name, 'STRONG') !== false) {
            return rand(6, 8);
        }
        if (strpos($name, 'DARK') !== false || strpos($name, 'STOUT') !== false) {
            return rand(5, 7);
        }
        if (strpos($name, 'IPA') !== false || strpos($name, 'PALE ALE') !== false) {
            return rand(5, 7);
        }
        if (strpos($name, 'WEIZEN') !== false || strpos($name, 'WEISSBIER') !== false) {
            return rand(5, 6);
        }
        if (strpos($name, 'LAGER') !== false || strpos($name, 'PILS') !== false) {
            return rand(4, 5);
        }
        
        return rand(4, 6); // Default
    }

    private function getBeerStyle($name)
    {
        $name = strtoupper($name);
        
        if (strpos($name, 'IPA') !== false) return 'ipa';
        if (strpos($name, 'PALE ALE') !== false) return 'pale_ale';
        if (strpos($name, 'STOUT') !== false) return 'stout';
        if (strpos($name, 'PORTER') !== false) return 'porter';
        if (strpos($name, 'WEIZEN') !== false || strpos($name, 'WEISSBIER') !== false) return 'wheat';
        if (strpos($name, 'PILS') !== false || strpos($name, 'PILSENER') !== false) return 'pilsner';
        if (strpos($name, 'DARK') !== false || strpos($name, 'DUNKEL') !== false) return 'dark';
        if (strpos($name, 'BLONDE') !== false || strpos($name, 'BLOND') !== false) return 'blonde';
        if (strpos($name, 'LAGER') !== false) return 'lager';
        if (strpos($name, 'ALE') !== false) return 'ale';
        
        return 'lager'; // Default
    }

    private function getBrewery($name)
    {
        // Extraer cervecería del nombre
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return $parts[0] . ' ' . $parts[1];
        }
        return $parts[0] ?? 'Desconocida';
    }

    private function mapCountry($country)
    {
        // Mantener los países exactamente como están en la lista
        return $country;
    }
}
