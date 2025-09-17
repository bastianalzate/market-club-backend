<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Obtener perfil completo del usuario con estadísticas
     */
    public function getProfile(Request $request)
    {
        $user = Auth::user();
        
        // Calcular estadísticas del usuario
        $stats = $this->getUserStats($user);
        
        // Obtener dirección del usuario (si existe)
        $address = $this->getUserAddress($user);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_wholesaler' => $user->is_wholesaler,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'profile_image' => $user->profile_image ?? null,
                'address' => $address,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Actualizar solo nombre y teléfono
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        // Guardar cambios
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta',
            ], 400);
        }

        // Actualizar contraseña
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }

    /**
     * Obtener órdenes del usuario con paginación
     */
    public function getOrders(Request $request)
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
    public function getOrder($orderId)
    {
        $user = Auth::user();
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
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
     * Obtener productos favoritos con paginación
     */
    public function getFavorites(Request $request)
    {
        $user = Auth::user();
        
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Wishlist::where('user_id', $user->id)
            ->with(['product.category'])
            ->orderBy('created_at', 'desc');

        // Buscar por nombre de producto
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $favorites = $query->paginate($perPage);

        // Formatear favoritos
        $formattedFavorites = $favorites->map(function ($favorite) {
            return [
                'id' => $favorite->id,
                'product_id' => $favorite->product_id,
                'user_id' => $favorite->user_id,
                'added_at' => $favorite->created_at,
                'product' => $this->formatProduct($favorite->product),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'favorites' => $formattedFavorites,
                'pagination' => [
                    'current_page' => $favorites->currentPage(),
                    'per_page' => $favorites->perPage(),
                    'total' => $favorites->total(),
                    'last_page' => $favorites->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Agregar producto a favoritos
     */
    public function addFavorite($productId)
    {
        $user = Auth::user();
        
        // Verificar que el producto existe y está activo
        $product = \App\Models\Product::where('id', $productId)
            ->where('is_active', true)
            ->first();
            
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible',
            ], 404);
        }

        // Verificar si ya está en favoritos
        if (\App\Models\Wishlist::isInWishlist($user->id, $productId)) {
            return response()->json([
                'success' => false,
                'message' => 'El producto ya está en tus favoritos',
            ], 400);
        }

        // Agregar a favoritos
        $wishlistItem = \App\Models\Wishlist::addProduct($user->id, $productId);

        // Obtener conteo actualizado
        $totalFavorites = \App\Models\Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado a favoritos',
            'data' => [
                'product_id' => $productId,
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
     * Remover producto de favoritos
     */
    public function removeFavorite($productId)
    {
        $user = Auth::user();
        
        // Verificar que el producto existe
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        // Remover de favoritos
        $removed = \App\Models\Wishlist::removeProduct($user->id, $productId);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en tus favoritos',
            ], 404);
        }

        // Obtener conteo actualizado
        $totalFavorites = \App\Models\Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Producto removido de favoritos',
            'data' => [
                'product_id' => $productId,
                'total_favorites' => $totalFavorites,
            ],
        ]);
    }

    /**
     * Obtener configuraciones del usuario
     */
    public function getSettings(Request $request)
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'notification_settings' => [
                    'email_notifications' => $user->email_notifications ?? true,
                    'sms_notifications' => $user->sms_notifications ?? false,
                    'order_updates' => $user->order_updates ?? true,
                    'promotions' => $user->promotions ?? true,
                    'newsletter' => $user->newsletter ?? true,
                ],
                'privacy_settings' => [
                    'profile_visibility' => $user->profile_visibility ?? 'private',
                    'show_orders' => $user->show_orders ?? false,
                    'show_favorites' => $user->show_favorites ?? false,
                ],
            ],
        ]);
    }

    /**
     * Actualizar configuraciones del usuario
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_settings' => 'sometimes|array',
            'notification_settings.email_notifications' => 'boolean',
            'notification_settings.sms_notifications' => 'boolean',
            'notification_settings.order_updates' => 'boolean',
            'notification_settings.promotions' => 'boolean',
            'notification_settings.newsletter' => 'boolean',
            'privacy_settings' => 'sometimes|array',
            'privacy_settings.profile_visibility' => 'in:public,private,friends',
            'privacy_settings.show_orders' => 'boolean',
            'privacy_settings.show_favorites' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        
        // Actualizar configuraciones
        if ($request->has('notification_settings')) {
            foreach ($request->notification_settings as $key => $value) {
                $user->$key = $value;
            }
        }
        
        if ($request->has('privacy_settings')) {
            foreach ($request->privacy_settings as $key => $value) {
                $user->$key = $value;
            }
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Configuraciones actualizadas exitosamente',
            'data' => [
                'notification_settings' => [
                    'email_notifications' => $user->email_notifications,
                    'sms_notifications' => $user->sms_notifications,
                    'order_updates' => $user->order_updates,
                    'promotions' => $user->promotions,
                    'newsletter' => $user->newsletter,
                ],
                'privacy_settings' => [
                    'profile_visibility' => $user->profile_visibility,
                    'show_orders' => $user->show_orders,
                    'show_favorites' => $user->show_favorites,
                ],
            ],
        ]);
    }

    /**
     * Obtener estadísticas del usuario
     */
    private function getUserStats($user)
    {
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $favoriteProductsCount = Wishlist::where('user_id', $user->id)->count();
        $lastOrder = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        return [
            'total_orders' => $totalOrders,
            'total_spent' => (float) $totalSpent,
            'favorite_products_count' => $favoriteProductsCount,
            'member_since' => $user->created_at,
            'last_order_date' => $lastOrder ? $lastOrder->created_at : null,
            'average_order_value' => (float) $averageOrderValue,
        ];
    }

    /**
     * Obtener dirección del usuario
     */
    private function getUserAddress($user)
    {
        if (!$user->address) {
            return null;
        }

        return [
            'street' => $user->address['street'] ?? '',
            'city' => $user->address['city'] ?? '',
            'state' => $user->address['state'] ?? '',
            'postal_code' => $user->address['postal_code'] ?? '',
            'country' => $user->address['country'] ?? '',
        ];
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
     * Formatear producto para respuesta
     */
    private function formatProduct($product)
    {
        $specificData = $product->product_specific_data ?? [];
        
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'current_price' => (float) ($product->sale_price ?? $product->price),
            'image' => $product->image,
            'image_url' => $product->image ? asset('storage/' . $product->image) : null,
            'brand' => $specificData['brewery'] ?? null,
            'category' => $product->category->name ?? null,
            'in_stock' => $product->stock_quantity > 0,
            'stock_quantity' => $product->stock_quantity,
            'description' => $product->description,
            'alcohol_content' => $specificData['alcohol_content'] ?? null,
            'volume' => $specificData['volume_ml'] ?? null,
            'origin' => $specificData['country_of_origin'] ?? null,
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
