<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            // Corregido: usar transacciones de pago reales en lugar de totales de órdenes
            'total_revenue' => PaymentTransaction::where('status', 'APPROVED')->sum('amount'),
            'today_sales' => PaymentTransaction::whereDate('created_at', today())
                ->where('status', 'APPROVED')
                ->sum('amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->count(),
            
            // Estadísticas de suscripciones
            'active_subscriptions' => UserSubscription::active()->count(),
            'total_subscriptions' => UserSubscription::count(),
            'subscription_revenue' => UserSubscription::where('status', 'active')->sum('price_paid'),
            'expiring_soon' => UserSubscription::expiringSoon(7)->count(),
        ];

        // Órdenes recientes
        $recent_orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Productos más vendidos
        $top_products = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        // Ventas por mes (últimos 6 meses) - Basado en transacciones de pago reales
        $dbDriver = config('database.default');
        if ($dbDriver === 'sqlite') {
            $monthly_sales = PaymentTransaction::selectRaw('strftime("%Y-%m", created_at) as month, SUM(amount) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->where('status', 'APPROVED')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthly_sales = PaymentTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->where('status', 'APPROVED')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return view('admin.dashboard.index', compact('stats', 'recent_orders', 'top_products', 'monthly_sales'));
    }

    /**
     * Exportar datos de ventas a CSV
     */
    public function exportSales(Request $request)
    {
        try {
            // Aumentar límites de memoria y tiempo de ejecución
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutos

            $period = $request->get('period', '12_months'); // 12_months, 6_months, 30_days, 7_days
            
            // Determinar fechas según el período
            switch ($period) {
                case '7_days':
                    $startDate = now()->subDays(7);
                    break;
                case '30_days':
                    $startDate = now()->subDays(30);
                    break;
                case '6_months':
                    $startDate = now()->subMonths(6);
                    break;
                case '12_months':
                default:
                    $startDate = now()->subMonths(12);
                    break;
            }

            // Mapeo de estados
            $statusLabels = [
                'pending' => 'Pendiente',
                'processing' => 'Procesando',
                'shipped' => 'Enviado',
                'delivered' => 'Entregado',
                'cancelled' => 'Cancelado',
            ];

            // Crear nombre del archivo
            $filename = 'ordenes_' . $period . '_' . now()->format('Y-m-d') . '.csv';

            // Headers para descarga
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            ];

            // Crear callback para generar CSV usando chunk para optimizar memoria
            $callback = function() use ($startDate, $statusLabels) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8 (para que Excel abra correctamente caracteres especiales)
                fwrite($file, "\xEF\xBB\xBF");

                // Encabezados de la tabla (según la imagen proporcionada)
                fputcsv($file, ['# DE ORDEN', 'CLIENTE', 'MONTO', 'PRODUCTOS', 'ESTADO']);

                // Procesar órdenes en chunks para optimizar memoria
                Order::with(['user', 'orderItems.product'])
                    ->where('created_at', '>=', $startDate)
                    ->orderBy('created_at', 'desc')
                    ->chunk(100, function ($orders) use ($file, $statusLabels) {
                        foreach ($orders as $order) {
                            // Obtener lista de productos con verificación de null
                            $products = $order->orderItems->map(function ($item) {
                                if ($item->product && $item->product->name) {
                                    return $item->product->name . ' (x' . $item->quantity . ')';
                                }
                                // Mostrar más información cuando el producto no se encuentra
                                $price = $item->unit_price ? '$' . number_format($item->unit_price, 0) : 'Precio N/A';
                                return "Producto eliminado o no disponible (x{$item->quantity}) - {$price}";
                            })->join(', ');

                            // Obtener estado traducido
                            $status = $statusLabels[$order->status] ?? ucfirst($order->status);

                            // Verificar que el usuario existe
                            $userName = 'Cliente no encontrado';
                            if ($order->user && $order->user->name) {
                                $userName = $order->user->name;
                            }

                            fputcsv($file, [
                                $order->order_number ?? 'N/A',
                                $userName,
                                '$' . number_format($order->total_amount ?? 0, 2),
                                $products,
                                $status
                            ]);
                        }
                    });

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error en exportación CSV: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al generar el archivo CSV. Por favor, inténtalo de nuevo.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
