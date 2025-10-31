<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductType extends Model
{
    use HasFactory;

    public const BEER_STYLE_OPTIONS = [
        'lager_clasica_pilsner' => 'Lager Clásica / Pilsner',
        'lager_oscura_fuerte' => 'Lager Oscura / Fuerte',
        'lager_ligera' => 'Lager Ligera',
        'ipa' => 'India Pale Ale (IPA)',
        'trigo_wheat' => 'Cerveza de Trigo (Wheat)',
        'ale_belga_clasica' => 'Ale Belga Clásica',
        'stout_porter' => 'Stout / Porter',
        'fruta_saborizada' => 'Cerveza de Fruta / Saborizada',
    ];

    private const LEGACY_BEER_STYLE_MAP = [
        'lager' => 'lager_clasica_pilsner',
        'pilsner' => 'lager_clasica_pilsner',
        'pilsener' => 'lager_clasica_pilsner',
        'ale' => 'ale_belga_clasica',
        'ipa' => 'ipa',
        'stout' => 'stout_porter',
        'porter' => 'stout_porter',
        'wheat' => 'trigo_wheat',
        'pale_ale' => 'ale_belga_clasica',
        'amber' => 'ale_belga_clasica',
        'brown' => 'ale_belga_clasica',
        'blonde' => 'lager_ligera',
        'dark' => 'lager_oscura_fuerte',
        'light' => 'lager_ligera',
        'craft' => 'ale_belga_clasica',
        'imported' => 'lager_clasica_pilsner',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields_config',
        'is_active',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getBeerStyleOptions(): array
    {
        return self::BEER_STYLE_OPTIONS;
    }

    public static function getBeerStyleLabel(string $value): string
    {
        return self::BEER_STYLE_OPTIONS[$value] ?? Str::of($value)->replace('_', ' ')->headline();
    }

    public static function normalizeBeerStyle(?string $style): ?string
    {
        if ($style === null) {
            return null;
        }

        $sanitized = self::sanitizeBeerStyle($style);

        if ($sanitized === '') {
            return null;
        }

        if (isset(self::BEER_STYLE_OPTIONS[$sanitized])) {
            return $sanitized;
        }

        if (isset(self::LEGACY_BEER_STYLE_MAP[$sanitized])) {
            return self::LEGACY_BEER_STYLE_MAP[$sanitized];
        }

        foreach (self::LEGACY_BEER_STYLE_MAP as $legacy => $normalized) {
            if (str_contains($sanitized, $legacy)) {
                return $normalized;
            }
        }

        return null;
    }

    public static function getBeerStyleAliases(string $normalized): array
    {
        $aliases = [$normalized];

        foreach (self::LEGACY_BEER_STYLE_MAP as $legacy => $mapped) {
            if ($mapped === $normalized) {
                $aliases[] = $legacy;
            }
        }

        return array_values(array_unique($aliases));
    }

    private static function sanitizeBeerStyle(string $style): string
    {
        $sanitized = Str::of($style)
            ->lower()
            ->ascii()
            ->replace(['/', '-'], ' ')
            ->squish()
            ->replace(' ', '_')
            ->value();

        $invalidValues = ['n_a', 'na', 'n\\a', 'sin_estilo', 'sin estilo', 'none', 'no_definido', 'no definido', ''];

        if (in_array($sanitized, $invalidValues, true)) {
            return '';
        }

        return $sanitized;
    }

    /**
     * Relación con productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Obtener configuración de campos para cervezas
     */
    public static function getBeerFieldsConfig(): array
    {
        return [
            'country' => [
                'type' => 'select',
                'label' => 'País de Origen',
                'required' => true,
                'options' => [
                    'Inglaterra' => 'Inglaterra',
                    'Colombia' => 'Colombia',
                    'Alemania' => 'Alemania',
                    'Italia' => 'Italia',
                    'Escocia' => 'Escocia',
                    'Bélgica' => 'Bélgica',
                    'España' => 'España',
                    'Países Bajos' => 'Países Bajos',
                    'Japón' => 'Japón',
                    'México' => 'México',
                    'Perú' => 'Perú',
                    'República Checa' => 'República Checa',
                    'Estados Unidos' => 'Estados Unidos',
                    'Tailandia' => 'Tailandia',
                ]
            ],
            'size_ml' => [
                'type' => 'select',
                'label' => 'Tamaño (ml)',
                'required' => true,
                'options' => [
                    '250' => '250 ml',
                    '330' => '330 ml',
                    '355' => '355 ml',
                    '473' => '473 ml',
                    '500' => '500 ml',
                    '650' => '650 ml',
                    '750' => '750 ml',
                    '1000' => '1000 ml',
                ]
            ],
            'container_type' => [
                'type' => 'select',
                'label' => 'Tipo de Envase',
                'required' => true,
                'options' => [
                    'botella' => 'Botella',
                    'lata' => 'Lata',
                    'barril' => 'Barril',
                    'growler' => 'Growler',
                ]
            ],
            'alcohol_content' => [
                'type' => 'number',
                'label' => 'Contenido de Alcohol (%)',
                'required' => false,
                'min' => 0,
                'max' => 100,
                'step' => 0.1,
            ],
            'beer_style' => [
                'type' => 'select',
                'label' => 'Estilo de Cerveza',
                'required' => false,
                'options' => self::BEER_STYLE_OPTIONS,
            ],
            'ibu' => [
                'type' => 'number',
                'label' => 'IBU (International Bitterness Units)',
                'required' => false,
                'min' => 0,
                'max' => 120,
            ],
            'srm' => [
                'type' => 'number',
                'label' => 'SRM (Standard Reference Method)',
                'required' => false,
                'min' => 1,
                'max' => 40,
            ],
            'brewery' => [
                'type' => 'text',
                'label' => 'Cervecería',
                'required' => false,
                'maxlength' => 255,
            ],
            'ingredients' => [
                'type' => 'textarea',
                'label' => 'Ingredientes',
                'required' => false,
                'rows' => 3,
            ],
            'tasting_notes' => [
                'type' => 'textarea',
                'label' => 'Notas de Cata',
                'required' => false,
                'rows' => 4,
            ],
        ];
    }

    /**
     * Obtener configuración de campos por tipo
     */
    public function getFieldsConfig(): array
    {
        if ($this->slug === 'cervezas') {
            return self::getBeerFieldsConfig();
        }

        return $this->fields_config ?? [];
    }

    /**
     * Verificar si es tipo cerveza
     */
    public function isBeerType(): bool
    {
        return $this->slug === 'cervezas';
    }

    /**
     * Obtener tipos de productos activos
     */
    public static function getActiveTypes()
    {
        return static::where('is_active', true)->orderBy('name')->get();
    }
}