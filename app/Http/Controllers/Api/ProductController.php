<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Filtro por categoría
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por país (nuevo)
        if ($request->has('country') && $request->country) {
            $normalizedCountry = $this->normalizeCountryName($request->country);
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.country_of_origin')) = ?", [$normalizedCountry]);
        }

        // Filtro por estilo de cerveza (nuevo)
        if ($request->has('beer_style') && $request->beer_style) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.beer_style')) = ?", [$request->beer_style]);
        }

        // Filtro por tipo de envase (nuevo)
        if ($request->has('packaging_type') && $request->packaging_type) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.packaging_type')) = ?", [$request->packaging_type]);
        }

        // Filtro por rango de precios (nuevo)
        if ($request->has('price_range') && $request->price_range) {
            $this->applyPriceFilter($query, $request->price_range);
        }

        // Filtro por búsqueda
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por productos destacados
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->get('per_page', 15));

        // Agregar información de favoritos
        $this->addFavoriteInfo($products->getCollection());

        return response()->json($products);
    }

    /**
     * Display featured products.
     */
    public function featured(Request $request)
    {
        $query = Product::where('is_active', true)
            ->where('is_featured', true);

        // Filtro por categoría
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por país (nuevo)
        if ($request->has('country') && $request->country) {
            $normalizedCountry = $this->normalizeCountryName($request->country);
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.country_of_origin')) = ?", [$normalizedCountry]);
        }

        // Filtro por estilo de cerveza (nuevo)
        if ($request->has('beer_style') && $request->beer_style) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.beer_style')) = ?", [$request->beer_style]);
        }

        // Filtro por rango de precios (nuevo)
        if ($request->has('price_range') && $request->price_range) {
            $this->applyPriceFilter($query, $request->price_range);
        }

        // Filtro por búsqueda
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Límite de productos destacados
        $limit = $request->get('limit', 10);
        $products = $query->limit($limit)->get();

        // Agregar información de favoritos
        $this->addFavoriteInfo($products);

        // Formatear la respuesta con solo los campos necesarios
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'current_price' => $product->current_price,
                'image_url' => $product->image_url,
                'stock_quantity' => $product->stock_quantity,
                'is_favorite' => $product->is_favorite ?? false,
            ];
        });

        return response()->json($formattedProducts);
    }

    /**
     * Display latest beers.
     */
    public function latestBeers(Request $request)
    {
        $query = Product::where('is_active', true)
            ->whereHas('productType', function ($q) {
                $q->where('name', 'Cervezas');
            });

        // Filtro por país (nuevo)
        if ($request->has('country') && $request->country) {
            $normalizedCountry = $this->normalizeCountryName($request->country);
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.country_of_origin')) = ?", [$normalizedCountry]);
        }

        // Filtro por estilo de cerveza (nuevo)
        if ($request->has('beer_style') && $request->beer_style) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(product_specific_data, '$.beer_style')) = ?", [$request->beer_style]);
        }

        // Filtro por rango de precios (nuevo)
        if ($request->has('price_range') && $request->price_range) {
            $this->applyPriceFilter($query, $request->price_range);
        }

        // Filtro por búsqueda
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenar por fecha de creación más reciente
        $query->orderBy('created_at', 'desc');

        // Límite de cervezas
        $limit = $request->get('limit', 10);
        $products = $query->limit($limit)->get();

        // Agregar información de favoritos
        $this->addFavoriteInfo($products);

        // Formatear la respuesta con solo los campos necesarios
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'current_price' => $product->current_price,
                'image_url' => $product->image_url,
                'stock_quantity' => $product->stock_quantity,
                'created_at' => $product->created_at->format('Y-m-d H:i:s'),
                'is_favorite' => $product->is_favorite ?? false,
            ];
        });

        return response()->json($formattedProducts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'is_featured' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        $product = Product::create($request->all());

        return response()->json($product->load('category'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Agregar información de favoritos para un solo producto
        $products = collect([$product]);
        $this->addFavoriteInfo($products);
        
        return response()->json($product->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        Log::info('=== INICIO ACTUALIZACIÓN PRODUCTO API ===');
        Log::info('Producto ID: ' . $product->id);
        Log::info('Producto nombre actual: ' . $product->name);
        Log::info('Usuario autenticado: ' . (Auth::check() ? Auth::user()->email : 'No autenticado'));
        Log::info('Datos recibidos en request: ' . json_encode($request->all()));
        
        try {
            Log::info('Iniciando validación de datos...');
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
                'stock_quantity' => 'sometimes|required|integer|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
                'image' => 'nullable|string',
                'gallery' => 'nullable|array',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
                'attributes' => 'nullable|array',
                'product_specific_data' => 'nullable|array',
            ]);
            Log::info('Validación exitosa');

            Log::info('Datos antes de la actualización:');
            Log::info('  - Nombre: ' . $product->name);
            Log::info('  - Precio: ' . $product->price);
            Log::info('  - Stock: ' . $product->stock_quantity);
            Log::info('  - Activo: ' . ($product->is_active ? 'true' : 'false'));
            Log::info('  - Datos específicos: ' . json_encode($product->product_specific_data));

            Log::info('Iniciando actualización del producto...');
            $updateResult = $product->update($request->all());
            Log::info('Resultado de la actualización: ' . ($updateResult ? 'true' : 'false'));

            // Recargar el producto para obtener los datos actualizados
            $product->refresh();
            
            Log::info('Datos después de la actualización:');
            Log::info('  - Nombre: ' . $product->name);
            Log::info('  - Precio: ' . $product->price);
            Log::info('  - Stock: ' . $product->stock_quantity);
            Log::info('  - Activo: ' . ($product->is_active ? 'true' : 'false'));
            Log::info('  - Datos específicos: ' . json_encode($product->product_specific_data));
            
            Log::info('=== FIN ACTUALIZACIÓN PRODUCTO API ===');

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $product->load('category')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en API: ' . json_encode($e->errors()));
            Log::info('=== FIN ACTUALIZACIÓN PRODUCTO API (ERROR VALIDACIÓN) ===');
            
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error general en actualización API: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::info('=== FIN ACTUALIZACIÓN PRODUCTO API (ERROR GENERAL) ===');

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar el producto'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Apply price range filter to query
     */
    private function applyPriceFilter($query, $priceRange)
    {
        switch ($priceRange) {
            case '0-10k':
                $query->where('price', '<=', 10000);
                break;
            case '10k-25k':
                $query->whereBetween('price', [10001, 25000]);
                break;
            case '25k-50k':
                $query->whereBetween('price', [25001, 50000]);
                break;
            case '50k+':
                $query->where('price', '>', 50000);
                break;
            // Mantener compatibilidad con formato anterior
            case 'less_than_15000':
                $query->where('price', '<', 15000);
                break;
            case '15000_25000':
                $query->whereBetween('price', [15000, 25000]);
                break;
            case '25000_35000':
                $query->whereBetween('price', [25000, 35000]);
                break;
            case '35000_50000':
                $query->whereBetween('price', [35000, 50000]);
                break;
            case '50000_75000':
                $query->whereBetween('price', [50000, 75000]);
                break;
            case '75000_100000':
                $query->whereBetween('price', [75000, 100000]);
                break;
            case 'more_than_100000':
                $query->where('price', '>', 100000);
                break;
        }
    }

    /**
     * Normalize country name from lowercase without accents to proper format
     */
    private function normalizeCountryName($country)
    {
        $countryMap = [
            'inglaterra' => 'Inglaterra',
            'reino unido' => 'Inglaterra',
            'colombia' => 'Colombia',
            'alemania' => 'Alemania',
            'italia' => 'Italia',
            'escocia' => 'Escocia',
            'belgica' => 'Bélgica',
            'espana' => 'España',
            'paises bajos' => 'Países Bajos',
            'japon' => 'Japón',
            'mexico' => 'México',
            'peru' => 'Perú',
            'republica checa' => 'República Checa',
            'estados unidos' => 'Estados Unidos',
            'tailandia' => 'Tailandia',
        ];

        $normalized = strtolower(trim($country));
        return $countryMap[$normalized] ?? $country;
    }

    /**
     * Add favorite information to products collection
     */
    private function addFavoriteInfo($products)
    {
        // Intentar obtener el usuario autenticado usando el token Bearer
        $user = null;
        $request = request();
        
        if ($request->bearerToken()) {
            try {
                $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
                if ($token) {
                    $user = $token->tokenable;
                }
            } catch (\Exception $e) {
                // Token inválido, continuar sin usuario
            }
        }
        
        if (!$user) {
            // Si no hay usuario autenticado, marcar todos como no favoritos
            $products->each(function ($product) {
                $product->is_favorite = false;
            });
            return;
        }

        // Obtener todos los IDs de productos
        $productIds = $products->pluck('id')->toArray();
        
        // Obtener los favoritos del usuario para estos productos
        $favoriteProductIds = Wishlist::where('user_id', $user->id)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();


        // Marcar cada producto como favorito o no
        $products->each(function ($product) use ($favoriteProductIds) {
            $product->is_favorite = in_array($product->id, $favoriteProductIds);
        });
    }

    /**
     * Get filter options for products
     */
    public function getFilters()
    {
        $products = Product::where('is_active', true)
            ->whereNotNull('product_specific_data')
            ->get();

        $countries = [];
        $beerStyles = [];
        $packagingTypes = [];
        $priceRanges = [];

        foreach ($products as $product) {
            $data = $product->product_specific_data;
            
            // Países de origen
            if (isset($data['country_of_origin']) && $data['country_of_origin']) {
                $countries[] = $data['country_of_origin'];
            }
            
            // Estilos de cerveza
            if (isset($data['beer_style']) && $data['beer_style']) {
                $beerStyles[] = $data['beer_style'];
            }
            
            // Tipos de envase
            if (isset($data['packaging_type']) && $data['packaging_type']) {
                $packagingTypes[] = $data['packaging_type'];
            }
            
            // Rangos de precio (simplificado)
            if ($product->price) {
                if ($product->price <= 10000) {
                    $priceRanges[] = '0-10k';
                } elseif ($product->price <= 25000) {
                    $priceRanges[] = '10k-25k';
                } elseif ($product->price <= 50000) {
                    $priceRanges[] = '25k-50k';
                } else {
                    $priceRanges[] = '50k+';
                }
            }
        }

        // Asegurar que siempre incluya todos los tipos de envase posibles
        $allPackagingTypes = ['lata', 'botella', 'barril', 'growler'];
        $finalPackagingTypes = array_unique(array_merge($packagingTypes, $allPackagingTypes));
        sort($finalPackagingTypes);

        return response()->json([
            'countries' => array_values(array_unique($countries)),
            'beer_styles' => array_values(array_unique($beerStyles)),
            'packaging_types' => array_values($finalPackagingTypes),
            'price_ranges' => array_values(array_unique($priceRanges))
        ]);
    }
}
