<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    use HasFactory;

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
                    'Alemania' => 'Alemania',
                    'Bélgica' => 'Bélgica',
                    'España' => 'España',
                    'China' => 'China',
                    'Japón' => 'Japón',
                    'Países Bajos' => 'Holanda',
                    'Reino Unido' => 'Escocia',
                    'Reino Unido' => 'Reino Unido',
                    'Tailandia' => 'Tailandia',
                    'México' => 'Mexico',
                    'Perú' => 'Peru',
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
                'options' => [
                    'lager' => 'Lager',
                    'pilsner' => 'Pilsner',
                    'ale' => 'Ale',
                    'ipa' => 'IPA',
                    'stout' => 'Stout',
                    'porter' => 'Porter',
                    'wheat' => 'Wheat Beer',
                    'pale_ale' => 'Pale Ale',
                    'amber' => 'Amber',
                    'brown' => 'Brown Ale',
                    'blonde' => 'Blonde',
                    'dark' => 'Dark Beer',
                    'light' => 'Light Beer',
                    'craft' => 'Craft Beer',
                    'imported' => 'Imported',
                ]
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