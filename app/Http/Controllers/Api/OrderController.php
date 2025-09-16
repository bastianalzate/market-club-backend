<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Obtener órdenes del usuario
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Order::where('user_id', $user->id)
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc');

        // Filtrar por estado
        if ($status) {
            $query->where('status', $status);
        }

        // Buscar por número de orden
        if ($search) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        $orders = $query->paginate($perPage);

        // Formatear órdenes
        $formattedOrders = $orders->map(function ($order) {
            return $this->formatOrder($order);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $formattedOrders,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Obtener detalles de una orden específica
     */
    public function show(string $id)
    {
        $user = Auth::user();
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->with(['orderItems.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order),
        ]);
    }

    /**
     * Formatear orden para respuesta
     */
    private function formatOrder($order)
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $this->translateOrderStatus($order->status),
            'total_amount' => (float) $order->total_amount,
            'subtotal' => (float) $order->subtotal,
            'tax_amount' => (float) $order->tax_amount,
            'shipping_amount' => (float) $order->shipping_amount,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'shipping_address' => $this->formatAddress($order->shipping_address),
            'tracking_number' => $order->tracking_number ?? null,
            'estimated_delivery' => $order->estimated_delivery ?? null,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                ];
            }),
        ];
    }

    /**
     * Formatear dirección para respuesta
     */
    private function formatAddress($address)
    {
        if (!$address) {
            return null;
        }

        return [
            'first_name' => $address['name'] ?? '',
            'last_name' => '',
            'address_line_1' => $address['address'] ?? '',
            'address_line_2' => null,
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'postal_code' => $address['postal_code'] ?? '',
            'country' => $address['country'] ?? '',
            'phone' => $address['phone'] ?? '',
        ];
    }

    /**
     * Traducir estado de orden
     */
    private function translateOrderStatus($status)
    {
        $statusMap = [
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'shipped' => 'En camino',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
        ];

        return $statusMap[$status] ?? $status;
    }
}
