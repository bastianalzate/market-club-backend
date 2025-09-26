<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Crear orden desde el carrito
     */
    public function createOrder(Request $request)
    {
        // Obtener usuario autenticado de manera opcional (similar a ProductController)
        $user = $this->getOptionalAuthenticatedUser($request);
        
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.address' => 'required|string|max:500',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'shipping_address.phone' => 'required|string|max:20',
            'billing_address' => 'nullable|array',
            'billing_address.name' => 'required_with:billing_address|string|max:255',
            'billing_address.email' => 'required_with:billing_address|email|max:255',
            'billing_address.address' => 'required_with:billing_address|string|max:500',
            'billing_address.city' => 'required_with:billing_address|string|max:100',
            'billing_address.state' => 'required_with:billing_address|string|max:100',
            'billing_address.postal_code' => 'required_with:billing_address|string|max:20',
            'billing_address.country' => 'required_with:billing_address|string|max:100',
            'billing_address.phone' => 'required_with:billing_address|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de envío inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $user ya fue obtenido al inicio del método
        $sessionId = $request->header('X-Session-ID');

        // Si no hay usuario autenticado y no hay session_id, devolver error
        if (!$user && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID requerido para usuarios no autenticados',
            ], 400);
        }

        // Obtener carrito activo
        $cart = Cart::getActiveCart($user?->id, $sessionId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Verificar stock de todos los productos (solo productos regulares, no regalos)
            foreach ($cart->items as $item) {
                // Saltar verificación de stock para regalos
                if ($item->is_gift || !$item->product) {
                    continue;
                }
                
                if ($item->product->stock_quantity < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto: {$item->product->name}",
                        'product' => $item->product->name,
                        'available_stock' => $item->product->stock_quantity,
                        'requested_quantity' => $item->quantity,
                    ], 400);
                }
            }

            // Recalcular totales del carrito antes de crear la orden
            $cart->calculateTotals();

            // Crear orden
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user?->id,
                'status' => 'pending',
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'total_amount' => $cart->total_amount,
                'payment_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Crear items de la orden
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'gift_id' => $item->gift_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'gift_data' => $item->gift_data,
                    'is_gift' => $item->is_gift,
                ]);

                // Reducir stock solo para productos regulares, no para regalos
                if (!$item->is_gift && $item->product) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                } elseif ($item->is_gift && $item->gift_data) {
                    // Para regalos, reducir stock de cada cerveza individual
                    $beers = $item->gift_data['beers'] ?? [];
                    foreach ($beers as $beer) {
                        $product = Product::find($beer['id']);
                        if ($product) {
                            $product->decrement('stock_quantity', $item->quantity);
                        }
                    }
                }
            }

            // Limpiar carrito
            $cart->clear();

            // Enviar email de confirmación
            $this->emailService->sendOrderConfirmation($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'data' => [
                    'order' => $order->load(['orderItems.product', 'user']),
                    'user_status' => [
                        'is_authenticated' => $user ? true : false,
                        'can_track_order' => $user ? true : false,
                        'login_suggestion' => $user ? null : 'Crea una cuenta para rastrear tu pedido y acceder a ofertas exclusivas'
                    ]
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener resumen del checkout
     */
    public function getCheckoutSummary(Request $request)
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

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío',
            ], 400);
        }

        // Verificar disponibilidad de productos (solo productos regulares, no regalos)
        $unavailableItems = [];
        foreach ($cart->items as $item) {
            // Saltar verificación para regalos
            if ($item->is_gift || !$item->product) {
                continue;
            }
            
            if ($item->product->stock_quantity < $item->quantity) {
                $unavailableItems[] = [
                    'product' => $item->product,
                    'requested_quantity' => $item->quantity,
                    'available_stock' => $item->product->stock_quantity,
                ];
            }
        }

        if (count($unavailableItems) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos productos no tienen stock suficiente',
                'unavailable_items' => $unavailableItems,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'items_count' => $cart->total_items,
                'subtotal' => $cart->subtotal,
                'tax_amount' => $cart->tax_amount,
                'shipping_amount' => $cart->shipping_amount,
                'total_amount' => $cart->total_amount,
                'shipping_free_threshold' => 100000,
                'is_shipping_free' => $cart->subtotal >= 100000,
                'user_status' => [
                    'is_authenticated' => $user ? true : false,
                    'login_benefits' => $user ? [] : [
                        'track_orders' => 'Rastrea tus pedidos en tiempo real',
                        'order_history' => 'Accede a tu historial de compras',
                        'wishlist' => 'Guarda productos en tu lista de deseos',
                        'faster_checkout' => 'Checkout más rápido con datos guardados',
                        'exclusive_offers' => 'Ofertas exclusivas para miembros'
                    ]
                ]
            ],
        ]);
    }

    /**
     * Validar dirección de envío
     */
    public function validateShippingAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.address' => 'required|string|max:500',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'shipping_address.phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección de envío inválida',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Aquí podrías agregar validaciones adicionales como:
        // - Verificar que la ciudad existe
        // - Validar código postal
        // - Calcular costo de envío
        // - etc.

        return response()->json([
            'success' => true,
            'message' => 'Dirección de envío válida',
            'data' => [
                'shipping_address' => $request->shipping_address,
                'estimated_shipping_cost' => 10000, // Costo estimado de envío
                'estimated_delivery_days' => 3, // Días estimados de entrega
            ],
        ]);
    }

    /**
     * Calcular costo de envío
     */
    public function calculateShipping(Request $request)
    {
        $user = $this->getOptionalAuthenticatedUser($request);
        
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de envío inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $user ya fue obtenido al inicio del método
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

        // Lógica simple de cálculo de envío
        $baseShipping = 10000; // $10,000 COP base
        $freeShippingThreshold = 100000; // Envío gratis sobre $100,000

        $shippingCost = 0;
        if ($cart->subtotal < $freeShippingThreshold) {
            // Aquí podrías agregar lógica más compleja basada en:
            // - Distancia
            // - Peso del paquete
            // - Tipo de envío
            // - etc.
            $shippingCost = $baseShipping;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'shipping_cost' => $shippingCost,
                'free_shipping_threshold' => $freeShippingThreshold,
                'is_free_shipping' => $cart->subtotal >= $freeShippingThreshold,
                'estimated_delivery_days' => 3,
                'delivery_options' => [
                    [
                        'type' => 'standard',
                        'name' => 'Envío Estándar',
                        'cost' => $shippingCost,
                        'days' => 3,
                    ],
                    [
                        'type' => 'express',
                        'name' => 'Envío Express',
                        'cost' => $shippingCost + 5000,
                        'days' => 1,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Sincronizar carrito de sesión con usuario autenticado
     */
    public function syncCartAfterLogin(Request $request)
    {
        $user = $this->getOptionalAuthenticatedUser($request);
        $sessionId = $request->header('X-Session-ID');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado',
            ], 401);
        }

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
            'message' => 'Carrito sincronizado exitosamente',
            'data' => [
                'cart' => $finalCart,
                'items_count' => $finalCart ? $finalCart->total_items : 0,
                'benefits_unlocked' => [
                    'track_orders' => 'Ahora puedes rastrear tus pedidos',
                    'order_history' => 'Accede a tu historial de compras',
                    'wishlist' => 'Guarda productos en tu lista de deseos',
                    'faster_checkout' => 'Checkout más rápido con datos guardados'
                ]
            ],
        ]);
    }

    /**
     * Get authenticated user optionally (similar to ProductController)
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