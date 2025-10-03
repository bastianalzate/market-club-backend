<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WholesalerCart;
use App\Models\Wholesaler;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WholesalerCartController extends Controller
{
    /**
     * Obtener el carrito del mayorista
     */
    public function index(Request $request)
    {
        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        // Si no hay mayorista autenticado y no hay session_id, devolver carrito vacío
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => null,
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'is_empty' => true,
                ],
            ]);
        }

        $cart = WholesalerCart::getOrCreateActiveCart($wholesaler?->id, $sessionId);

        // Procesar items para incluir datos del producto desde snapshot si no hay relación
        $processedItems = $this->processCartItems($cart);

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $cart->setRelation('items', $processedItems),
                'items_count' => $cart->total_items,
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'discount_amount' => $cart->discount_amount,
                'total_amount' => $cart->total_amount,
                'is_empty' => $cart->isEmpty(),
                'wholesaler_discount' => 15, // 15% de descuento por defecto
            ],
        ]);
    }

    /**
     * Agregar producto al carrito de mayorista
     */
    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100', // Mayor cantidad para mayoristas
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Si no hay mayorista autenticado y no hay session_id, devolver error
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        // Verificar que el mayorista esté habilitado si está autenticado
        if ($wholesaler && !$wholesaler->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'El mayorista no está habilitado para realizar compras',
            ], 403);
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

        // Verificar stock (mayoristas pueden comprar más cantidad)
        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente',
                'available_stock' => $product->stock_quantity,
            ], 400);
        }

        $cart = WholesalerCart::getOrCreateActiveCart($wholesaler?->id, $sessionId);
        $cart->addProduct($productId, $quantity);

        // Recargar carrito con items y procesar para incluir datos del producto
        $freshCart = $cart->fresh(['items.product']);
        $processedItems = $this->processCartItems($freshCart);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito mayorista',
            'data' => [
                'cart' => $freshCart->setRelation('items', $processedItems),
                'items_count' => $freshCart->total_items,
                'wholesaler_discount' => 15,
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
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Si no hay mayorista autenticado y no hay session_id, devolver error
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

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

        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');
        $productId = $request->product_id;

        // Si no hay mayorista autenticado y no hay session_id, devolver error
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

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
        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        // Si no hay mayorista autenticado y no hay session_id, devolver error
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

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
        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        // Si no hay mayorista autenticado y no hay session_id, devolver carrito vacío
        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'is_empty' => true,
                ],
            ]);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items_count' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'discount_amount' => 0,
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
                'discount_amount' => $cart->discount_amount,
                'total_amount' => $cart->total_amount,
                'is_empty' => false,
                'wholesaler_discount' => 15,
            ],
        ]);
    }

    /**
     * Aplicar descuento especial
     */
    public function applyDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado',
            ], 404);
        }

        $cart->applyDiscount($request->discount_amount, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Descuento aplicado',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'discount_amount' => $cart->discount_amount,
            ],
        ]);
    }

    /**
     * Agregar notas al carrito
     */
    public function addNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        if (!$wholesaler && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para mayoristas no autenticados',
            ], 400);
        }

        $cart = WholesalerCart::getActiveCart($wholesaler?->id, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado',
            ], 404);
        }

        $cart->addNotes($request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Notas agregadas al carrito',
            'data' => [
                'cart' => $cart->fresh(['items.product']),
                'notes' => $cart->notes,
            ],
        ]);
    }

    /**
     * Sincronizar carrito de sesión con mayorista autenticado
     */
    public function sync(Request $request)
    {
        $wholesaler = $this->getOptionalAuthenticatedWholesaler($request);
        $sessionId = $request->header('X-Session-ID');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido',
            ], 400);
        }

        if (!$wholesaler) {
            return response()->json([
                'success' => false,
                'message' => 'Mayorista no autenticado',
            ], 401);
        }

        // Obtener carrito de sesión
        $sessionCart = WholesalerCart::getActiveCart(null, $sessionId);
        
        // Obtener carrito del mayorista
        $wholesalerCart = WholesalerCart::getActiveCart($wholesaler->id, null);

        if ($sessionCart && $sessionCart->items()->count() > 0) {
            if ($wholesalerCart) {
                // Fusionar carritos
                foreach ($sessionCart->items as $sessionItem) {
                    $wholesalerCart->addProduct($sessionItem->product_id, $sessionItem->quantity);
                }
                $sessionCart->delete();
            } else {
                // Asignar carrito de sesión al mayorista
                $sessionCart->update(['wholesaler_id' => $wholesaler->id, 'session_id' => null]);
            }
        }

        $finalCart = WholesalerCart::getActiveCart($wholesaler->id, null);

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
     * Get authenticated wholesaler optionally
     */
    private function getOptionalAuthenticatedWholesaler($request)
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

        // Verificar si el usuario es un mayorista
        if ($user && $user->isWholesaler()) {
            return Wholesaler::where('email', $user->email)->first();
        }
        
        return null;
    }

    /**
     * Procesar items del carrito para incluir datos del producto desde snapshot
     */
    private function processCartItems($cart)
    {
        return $cart->items->map(function ($item) {
            if (!$item->product && $item->product_snapshot) {
                // Si no hay relación product cargada, usar el snapshot
                $item->product = (object) $item->product_snapshot;
            }
            return $item;
        });
    }
}
