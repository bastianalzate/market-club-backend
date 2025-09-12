<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Crear orden desde el carrito
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.address' => 'required|string|max:500',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'shipping_address.phone' => 'required|string|max:20',
            'billing_address' => 'nullable|array',
            'billing_address.name' => 'required_with:billing_address|string|max:255',
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

        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        // Obtener carrito activo
        $cart = Cart::getActiveCart($user->id, $sessionId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Verificar stock de todos los productos
            foreach ($cart->items as $item) {
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

            // Crear orden
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
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
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);

                // Reducir stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Limpiar carrito
            $cart->clear();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'data' => [
                    'order' => $order->load(['orderItems.product', 'user']),
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
        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');

        $cart = Cart::getActiveCart($user->id, $sessionId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío',
            ], 400);
        }

        // Verificar disponibilidad de productos
        $unavailableItems = [];
        foreach ($cart->items as $item) {
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

        $user = Auth::user();
        $sessionId = $request->header('X-Session-ID');
        $cart = Cart::getActiveCart($user->id, $sessionId);

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
}