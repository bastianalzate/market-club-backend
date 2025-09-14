<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Obtener el carrito del usuario
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        // Si no hay usuario autenticado y no hay session_id, devolver carrito vacío
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => null,
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'total_amount' => 0,
                    'is_empty' => true,
                ],
            ]);
        }

        $cart = Cart::getOrCreateActiveCart($user?->id, $sessionId);

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'items_count' => $cart->total_items,
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'total_amount' => $cart->total_amount,
                'is_empty' => $cart->isEmpty(),
            ],
        ]);
    }

    /**
     * Agregar producto al carrito
     */
    public function addProduct(Request $request)
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
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
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

        $cart = Cart::getOrCreateActiveCart($user?->id, $sessionId);
        $cart->addProduct($productId, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'items_count' => $cart->total_items,
            ],
        ]);
    }

    /**
     * Actualizar cantidad de un producto en el carrito
     */
    public function updateQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
        }

        $cart = Cart::getActiveCart($user?->id, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado',
            ], 404);
        }

        // Verificar stock si se está aumentando la cantidad
        if ($quantity > 0) {
            $product = Product::find($productId);
            $currentQuantity = $cart->items()
                ->where('product_id', $productId)
                ->value('quantity') ?? 0;

            if ($quantity > $currentQuantity && $product->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente',
                    'available_stock' => $product->stock_quantity,
                ], 400);
            }
        }

        $cart->updateProductQuantity($productId, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'items_count' => $cart->total_items,
            ],
        ]);
    }

    /**
     * Remover producto del carrito
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
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
        }

        $cart = Cart::getActiveCart($user?->id, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado',
            ], 404);
        }

        $cart->removeProduct($productId);

        return response()->json([
            'success' => true,
            'message' => 'Producto removido del carrito',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'items_count' => $cart->total_items,
            ],
        ]);
    }

    /**
     * Limpiar carrito
     */
    public function clear(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
        }

        $cart = Cart::getActiveCart($user?->id, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado',
            ], 404);
        }

        $cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Carrito limpiado',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'items_count' => $cart->total_items,
            ],
        ]);
    }

    /**
     * Obtener resumen del carrito
     */
    public function summary(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        // Si no hay usuario autenticado y no hay session_id, devolver carrito vacío
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'total_amount' => 0,
                    'is_empty' => true,
                ],
            ]);
        }

        $cart = Cart::getActiveCart($user?->id, $sessionId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'total_amount' => 0,
                    'is_empty' => true,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items_count' => $cart->total_items,
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'total_amount' => $cart->total_amount,
                'is_empty' => false,
            ],
        ]);
    }

    /**
     * Sincronizar carrito de sesión con usuario autenticado
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido',
            ], 400);
        }

        // Obtener carrito de sesión
        $sessionCart = Cart::getActiveCart(null, $sessionId);
        
        // Obtener carrito del usuario
        $userCart = Cart::getActiveCart($user->id, null);

        if ($sessionCart && $sessionCart->items()->count() > 0) {
            if ($userCart) {
                // Fusionar carritos
                foreach ($sessionCart->items as $sessionItem) {
                    $userCart->addProduct($sessionItem->product_id, $sessionItem->quantity);
                }
                $sessionCart->delete();
            } else {
                // Asignar carrito de sesión al usuario
                $sessionCart->update(['user_id' => $user->id, 'session_id' => null]);
            }
        }

        $finalCart = Cart::getActiveCart($user->id, null);

        return response()->json([
            'success' => true,
            'message' => 'Carrito sincronizado',
            'data' => [
                'cart' => $finalCart,
                'items_count' => $finalCart ? $finalCart->total_items : 0,
            ],
        ]);
    }
}