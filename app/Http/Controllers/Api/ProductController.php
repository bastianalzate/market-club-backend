<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        // Formatear la respuesta con solo los campos necesarios
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'current_price' => $product->current_price,
                'image_url' => $product->image_url,
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

        // Formatear la respuesta con solo los campos necesarios
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'current_price' => $product->current_price,
                'image_url' => $product->image_url,
                'created_at' => $product->created_at->format('Y-m-d H:i:s'),
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
        return response()->json($product->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
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
            'attributes' => 'nullable|array',
        ]);

        $product->update($request->all());

        return response()->json($product->load('category'));
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
            'inglaterra' => 'Reino Unido',
            'reino unido' => 'Reino Unido',
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
}
