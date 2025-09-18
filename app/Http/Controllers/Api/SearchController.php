<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * Búsqueda general de productos
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:100',
            'category' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|in:name,price,created_at,popularity',
            'order' => 'nullable|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = $request->q;
        $categoryId = $request->category;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';
        $perPage = $request->per_page ?? 15;

        // Construir consulta
        $productsQuery = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            });

        // Filtro por categoría
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        // Filtro por precio
        if ($minPrice !== null) {
            $productsQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Ordenamiento
        switch ($sort) {
            case 'name':
                $productsQuery->orderBy('name', $order);
                break;
            case 'price':
                $productsQuery->orderBy('price', $order);
                break;
            case 'popularity':
                $productsQuery->orderBy('created_at', $order);
                break;
            default:
                $productsQuery->orderBy('created_at', $order);
        }

        $products = $productsQuery->with(['category'])
            ->paginate($perPage);

        // Agregar información de favoritos
        $this->addFavoriteInfo($products->items());

        // Obtener categorías para filtros
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'search_params' => [
                    'query' => $query,
                    'category' => $categoryId,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'sort' => $sort,
                    'order' => $order,
                ],
                'total_results' => $products->total(),
            ],
        ]);
    }

    /**
     * Búsqueda de sugerencias (autocompletado)
     */
    public function suggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:1|max:50',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = $request->q;
        $limit = $request->limit ?? 5;

        // Buscar productos
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'sku', 'price', 'image')
            ->limit($limit)
            ->get();

        // Buscar categorías
        $categories = Category::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'slug')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'query' => $query,
            ],
        ]);
    }

    /**
     * Productos destacados
     */
    public function featured(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $limit = $request->limit ?? 8;

        $products = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Agregar información de favoritos
        $this->addFavoriteInfo($products);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'count' => $products->count(),
            ],
        ]);
    }

    /**
     * Productos relacionados
     */
    public function related(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $limit = $request->limit ?? 4;

        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Agregar información de favoritos
        $this->addFavoriteInfo($relatedProducts);

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts,
                'count' => $relatedProducts->count(),
            ],
        ]);
    }

    /**
     * Agregar información de favoritos a los productos
     */
    private function addFavoriteInfo($products)
    {
        // Obtener usuario autenticado de manera opcional
        $user = $this->getOptionalAuthenticatedUser();

        if (!$user) {
            // Si no hay usuario autenticado, marcar todos como no favoritos
            foreach ($products as $product) {
                $product->is_favorite = false;
            }
            return;
        }

        // Obtener IDs de productos favoritos del usuario
        $favoriteProductIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->toArray();

        // Agregar el campo is_favorite a cada producto
        foreach ($products as $product) {
            $product->is_favorite = in_array($product->id, $favoriteProductIds);
        }
    }

    /**
     * Obtener usuario autenticado de manera opcional
     */
    private function getOptionalAuthenticatedUser()
    {
        try {
            // Verificar si hay un token Bearer en la petición
            $token = request()->bearerToken();
            if (!$token) {
                return null;
            }

            // Intentar autenticar usando Sanctum
            $user = Auth::guard('sanctum')->user();
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }
}