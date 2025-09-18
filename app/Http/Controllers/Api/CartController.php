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
        $user = $this->getOptionalAuthenticatedUser($request);
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
     * Agregar regalo personalizado al carrito
     */
    public function addGift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string', // Para regalos puede ser un ID temporal
            'quantity' => 'integer|min:1|max:10',
            'gift_data' => 'required|array',
            'gift_data.name' => 'required|string|max:255',
            'gift_data.description' => 'required|string',
            'gift_data.box' => 'required|array',
            'gift_data.box.id' => 'required|string',
            'gift_data.box.name' => 'required|string',
            'gift_data.box.price' => 'required|numeric|min:0',
            'gift_data.beers' => 'required|array|min:1',
            'gift_data.beers.*.id' => 'required|exists:products,id',
            'gift_data.totalPrice' => 'required|numeric|min:0',
            'is_gift' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->getOptionalAuthenticatedUser($request);
        $sessionId = $request->header('X-Session-ID');
        $quantity = $request->quantity ?? 1;

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
        }

        // Verificar que todas las cervezas existen y están disponibles
        $beerIds = collect($request->gift_data['beers'])->pluck('id')->toArray();
        $availableBeers = Product::whereIn('id', $beerIds)
            ->where('is_active', true)
            ->get();

        if ($availableBeers->count() !== count($beerIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Una o más cervezas no están disponibles',
            ], 400);
        }

        // Verificar stock de las cervezas
        foreach ($request->gift_data['beers'] as $beer) {
            $product = $availableBeers->firstWhere('id', $beer['id']);
            if ($product->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para {$product->name}",
                    'available_stock' => $product->stock_quantity,
                ], 400);
            }
        }

        $cart = Cart::getOrCreateActiveCart($user?->id, $sessionId);
        $cart->addGift($request->product_id, $quantity, $request->gift_data);
        
        // Recargar carrito con totales actualizados
        $cart = $cart->fresh(['items']);

        return response()->json([
            'success' => true,
            'message' => 'Regalo agregado al carrito',
            'data' => [
                'cart' => $cart,
                'items_count' => $cart->total_items,
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

        $user = $this->getOptionalAuthenticatedUser($request);
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
            'product_id' => 'required_without:gift_id|exists:products,id',
            'gift_id' => 'required_without:product_id|string',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->getOptionalAuthenticatedUser($request);
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $giftId = $request->gift_id;
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

        // Verificar stock si se está aumentando la cantidad (solo para productos, no regalos)
        if ($quantity > 0 && $productId) {
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

        if ($productId) {
            $cart->updateProductQuantity($productId, $quantity);
        } else {
            $cart->updateGiftQuantity($giftId, $quantity);
        }

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
            'product_id' => 'required_without:gift_id|exists:products,id',
            'gift_id' => 'required_without:product_id|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->getOptionalAuthenticatedUser($request);
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $giftId = $request->gift_id;

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

        if ($productId) {
            $cart->removeProduct($productId);
        } else {
            $cart->removeGift($giftId);
        }

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
        $user = $this->getOptionalAuthenticatedUser($request);
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
        $user = $this->getOptionalAuthenticatedUser($request);
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
        $user = $this->getOptionalAuthenticatedUser($request);
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

    /**
     * Get authenticated user optionally (similar to ProductController and CheckoutController)
     */
    private function getOptionalAuthenticatedUser($request)
    {
        $user = null;
        
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
        
        return $user;
    }
}