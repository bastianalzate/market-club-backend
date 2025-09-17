<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Obtener wishlist del usuario
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $wishlist = Wishlist::getUserWishlist($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $wishlist,
                'items_count' => $wishlist->count(),
            ],
        ]);
    }

    /**
     * Agregar producto a la wishlist
     */
    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $productId = $request->product_id;

        // Verificar que el producto esté disponible
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible',
            ], 404);
        }

        // Verificar si ya está en la wishlist
        if (Wishlist::isInWishlist($user->id, $productId)) {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya está en tu wishlist',
            ], 400);
        }

        $wishlistItem = Wishlist::addProduct($user->id, $productId);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado a la wishlist',
            'data' => [
                'wishlist_item' => $wishlistItem->load('product'),
            ],
        ]);
    }

    /**
     * Remover producto de la wishlist
     */
    public function removeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $productId = $request->product_id;

        $removed = Wishlist::removeProduct($user->id, $productId);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en tu wishlist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto removido de la wishlist',
        ]);
    }

    /**
     * Verificar si un producto está en la wishlist
     */
    public function checkProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $productId = $request->product_id;

        $isInWishlist = Wishlist::isInWishlist($user->id, $productId);

        return response()->json([
            'success' => true,
            'data' => [
                'is_in_wishlist' => $isInWishlist,
            ],
        ]);
    }

    /**
     * Limpiar wishlist
     */
    public function clear(Request $request)
    {
        $user = Auth::user();
        
        $deleted = Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist limpiada',
            'data' => [
                'deleted_items' => $deleted,
            ],
        ]);
    }

    /**
     * Toggle producto en la wishlist (agregar o quitar)
     */
    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $productId = $request->product_id;

        // Verificar que el producto esté disponible
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible',
            ], 404);
        }

        // Verificar si está en la wishlist
        $isInWishlist = Wishlist::isInWishlist($user->id, $productId);

        if ($isInWishlist) {
            // Remover de la wishlist
            Wishlist::removeProduct($user->id, $productId);
            $action = 'removed';
            $message = 'Producto removido de favoritos';
        } else {
            // Agregar a la wishlist
            $wishlistItem = Wishlist::addProduct($user->id, $productId);
            $action = 'added';
            $message = 'Producto agregado a favoritos';
        }

        // Obtener conteo actualizado
        $totalFavorites = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'action' => $action,
                'is_in_wishlist' => !$isInWishlist,
                'total_favorites' => $totalFavorites,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image_url,
                ],
            ],
        ]);
    }

    /**
     * Mover producto de wishlist al carrito
     */
    public function moveToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Verificar que el producto esté en la wishlist
        if (!Wishlist::isInWishlist($user->id, $productId)) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en tu wishlist',
            ], 404);
        }

        // Verificar que el producto esté disponible
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible',
            ], 404);
        }

        // Verificar stock
        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente',
                'available_stock' => $product->stock_quantity,
            ], 400);
        }

        // Agregar al carrito
        $cart = \App\Models\Cart::getOrCreateActiveCart($user->id);
        $cart->addProduct($productId, $quantity);

        // Remover de la wishlist
        Wishlist::removeProduct($user->id, $productId);

        return response()->json([
            'success' => true,
            'message' => 'Producto movido al carrito',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'items_count' => $cart->total_items,
            ],
        ]);
    }
}