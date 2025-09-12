<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateBeerCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapeo de productos con sus países correctos según el orden del Excel
        $productCountries = [
            'ADNAMS GHOST SHIP' => 'Inglaterra',
            'ADNAMS KOBOLD ENGLISH LAGER' => 'Inglaterra',
            'AGUILA ORIGINAL LATA X 330ML' => 'Colombia',
            'ANDINA DORADA' => 'Colombia',
            'ARTESANAL BLONDE ALE' => 'Colombia',
            'ARTESANAL PORTER' => 'Colombia',
            'ARTESANAL SAISON' => 'Colombia',
            'BARRIL BITBURGER 5LTR' => 'Alemania',
            'BENEDIK TINER BARRILITO 5 LTRS' => 'Alemania',
            'BENEDIKTINER LATA 500ML' => 'Alemania',
            'BIRRA PERONI BOTELLA 330' => 'Italia',
            'BITBURGER DRIVE 0.0%' => 'Alemania',
            'BITBURGER PREMIUM PILS' => 'Alemania',
            'BITBURGER PREMIUM X 500 ML' => 'Alemania',
            'BITBURGUER LATA + 13,5 FREE' => 'Alemania',
            'BREWDOG ELVIS JUICE LATA' => 'Escocia',
            'BREWDOG HAZY JANE' => 'Escocia',
            'BREWDOG HAZY JANE LATA 330ML' => 'Escocia',
            'BRUDER MANGO GRANADILLA' => 'Colombia',
            'BRUGSE ZOT BLOND 330' => 'Bélgica',
            'CERVERZA HOFBRAU MUNCHEN BOTELLA X 500 ML' => 'Alemania',
            'CERVEZA 1906' => 'España',
            'CERVEZA 8.6 BLACK DARK BEER X 500 ML' => 'Países Bajos',
            'CERVEZA 8.6 EXTREME STRONG BEER X 500 ML' => 'Países Bajos',
            'CERVEZA 8.6 ORIGINAL BLOND BEER X 500 ML' => 'Países Bajos',
            'CERVEZA 8.6 RED RED BEER X 500 ML' => 'Países Bajos',
            'CERVEZA ARTESANAL IPA' => 'Colombia',
            'CERVEZA ASAHI SUPER DRY BOTELLA 330' => 'Japón',
            'CERVEZA BBC UNIDAD' => 'Colombia',
            'CERVEZA CORONA EXTRA 330 ML' => 'México',
            'CERVEZA COSTEÑA LATA X 330 ML' => 'Colombia',
            'CERVEZA CUSQUEÑA' => 'Perú',
            'CERVEZA CZECHVAR CZECH LAGER X 330 ML' => 'República Checa',
            'CERVEZA DUNKEL ERDINGER X 500 ML' => 'Alemania',
            'CERVEZA DUVEL BELGIAN GOLDEN ALE X 330 ML' => 'Bélgica',
            'CERVEZA ERDINGER WEIBBIER' => 'Alemania',
            'CERVEZA ESTRELLA GALICIA  XUND' => 'España',
            'CERVEZA ESTRELLA GALICIA LATA' => 'España',
            'CERVEZA GERMAN RED STEAM BREW X 500 ML' => 'Alemania',
            'CERVEZA GULDEN DRAAK X UND' => 'Bélgica',
            'CERVEZA HOLLANDIA PREMIUM' => 'Países Bajos',
            'CERVEZA IRON MAIDEN X 500 ML' => 'Inglaterra',
            'CERVEZA LA CHOUFFE 40 X UND' => 'Bélgica',
            'CERVEZA LIEFMANS FRUITESSE BOTELLA X 250 ML' => 'Bélgica',
            'CERVEZA MAHOU' => 'España',
            'CERVEZA MODELO ESPECIAL' => 'México',
            'CERVEZA PALE ALE STEAM BREW X 500 ML' => 'Alemania',
            'CERVEZA PILSEN LATA X 330' => 'Colombia',
            'CERVEZA POKER LATA X 330 ML' => 'Colombia',
            'CERVEZA REDDS LATA X 269 ML' => 'Colombia',
            'CERVEZA SOL BNR 330' => 'México',
            'CERVEZA STEAM BREW 500ML XUND IMPERIAL IPA' => 'Alemania',
            'CERVEZA STEAM BREW X 500 ML' => 'Alemania',
            'CERVEZA STELLA ARTOIS' => 'Bélgica',
            'CERVEZA TEKATE' => 'México',
            'CERVEZA WEIDMANN X 500 ML' => 'Alemania',
            'CLUB COLOMBIA DORADA LATA X 330 ML' => 'Colombia',
            'DAB LATA' => 'Alemania',
            'DELIRIUM ARGENTUM' => 'Bélgica',
            'DELIRIUM CHRISTMAS' => 'Bélgica',
            'DELIRIUM RED' => 'Bélgica',
            'DELIRIUM TREMEMS BOTELLA NR 330ML X UND' => 'Bélgica',
            'EICHBAUM DUNKEL LATA' => 'Alemania',
            'ERDINGER ALKOHOL FREE BOT 500ML' => 'Alemania',
            'ERDINGER URWEISSE BOT 500' => 'Alemania',
            'ERDONGER PIKANTUS BOTELLA NR 500ML X UND' => 'Alemania',
            'FLENSBURGER DUNKEL' => 'Alemania',
            'FLENSBURGER PILSENER' => 'Alemania',
            'FLENSBURGER WEIZEN' => 'Alemania',
            'FLORIS FRAMBOISE BOTELLA  NR 330ML X UND' => 'Bélgica',
            'GERMÁN BEBER AC DC' => 'Alemania',
            'GOLD INTENSE BEER' => 'Alemania',
            'HB HOFBRAU DUNKEL' => 'Alemania',
            'HEINEKEN X 330 BOTELLA VIDRIO' => 'Países Bajos',
            'INDICA IPA COAST BOT X 355' => 'Estados Unidos',
            'INNIS AND GUNN ORIGINAL' => 'Escocia',
            'KARLSBRAU LAGER' => 'Alemania',
            'KARLSBRAU URPILS' => 'Alemania',
            'KOSTRITZER BARRILITO 5LTRS' => 'Alemania',
            'KOSTRITZER LATA 500ML' => 'Alemania',
            'KRISTOFFEL BLOND' => 'Bélgica',
            'KRISTOFFEL ROSE' => 'Bélgica',
            'KRISTOFFEL WHITE' => 'Bélgica',
            'LAGER BEER SHINGHA LATA X 330ML' => 'Tailandia',
            'LAGER BEER SINGHA BOTELLA X330ML' => 'Tailandia',
            'MILLER LITE LATA X310' => 'Estados Unidos',
            'PAULANER WEISSBIER LATA  500  ML' => 'Alemania',
            'PILSEN LATON' => 'Colombia',
            'REEPER B.WEISSBIER LATA' => 'Alemania',
            'SAPPORO PREMIUM BEER' => 'Japón',
            'SCHOFFERHOFER' => 'Alemania',
            'SCHOFFERHOFER TORONJA GRAPEFRUIT' => 'Alemania',
            'ST.IDESBALD  BOT 330' => 'Bélgica',
            'STRAFFE HENDRIK TRIPEL X 330' => 'Bélgica',
            'TANGERINE LOST COAST' => 'Estados Unidos',
            'TRES CORDILLERAS VIDRIO X300ML' => 'Colombia',
            'TROOPER FEAR OF THE DARK LATA 500 ML' => 'Inglaterra',
            'TROOPER IPA BOTELLA 500  ML' => 'Inglaterra',
            'WHEAT PALE ALE STEAM BREW X 500 ML' => 'Alemania',
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($productCountries as $productName => $country) {
            $product = Product::where('name', $productName)->first();
            
            if ($product) {
                $currentData = $product->product_specific_data ?? [];
                $currentData['country_of_origin'] = $country;
                
                $product->update([
                    'product_specific_data' => $currentData
                ]);
                
                $updated++;
                $this->command->info("✓ Actualizado: {$productName} -> {$country}");
            } else {
                $notFound++;
                $this->command->warn("✗ No encontrado: {$productName}");
            }
        }

        $this->command->info("Actualización completada:");
        $this->command->info("- Productos actualizados: {$updated}");
        $this->command->info("- Productos no encontrados: {$notFound}");
    }
}