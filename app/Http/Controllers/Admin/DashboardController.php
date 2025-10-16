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

        // Obtener órdenes detalladas con información completa
        $orders = Order::with(['user', 'orderItems.product', 'paymentTransaction'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener datos de ventas diarias
        $dailySales = PaymentTransaction::selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(amount) as total_amount')
            ->where('created_at', '>=', $startDate)
            ->where('status', 'APPROVED')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Obtener datos de productos vendidos
        $productSales = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('payment_transactions', 'orders.id', '=', 'payment_transactions.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->where('payment_transactions.created_at', '>=', $startDate)
            ->where('payment_transactions.status', 'APPROVED')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Crear nombre del archivo
        $filename = 'reporte_ventas_' . $period . '_' . now()->format('Y-m-d') . '.csv';

        // Headers para descarga
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Crear callback para generar CSV
        $callback = function() use ($orders, $dailySales, $productSales, $period) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8 (para que Excel abra correctamente caracteres especiales)
            fwrite($file, "\xEF\xBB\xBF");

            // Encabezado del reporte
            fputcsv($file, ['REPORTE DE VENTAS - MARKET CLUB']);
            fputcsv($file, ['Período:', $period]);
            fputcsv($file, ['Generado:', now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []); // Línea vacía

            // Tabla principal con solo las 5 columnas requeridas
            fputcsv($file, ['# DE ORDEN', 'CLIENTE', 'MONTO', 'PRODUCTOS', 'ESTADO']);
            
            foreach ($orders as $order) {
                // Obtener lista de productos
                $products = $order->orderItems->map(function ($item) {
                    return $item->product->name . ' (x' . $item->quantity . ')';
                })->implode(', ');
                
                // Obtener estado en español
                $statusLabels = [
                    'pending' => 'Pendiente',
                    'processing' => 'Procesando',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregado',
                    'cancelled' => 'Cancelado',
                ];
                $status = $statusLabels[$order->status] ?? ucfirst($order->status);
                
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name ?? 'Cliente no registrado',
                    number_format($order->total_amount, 2),
                    $products,
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
