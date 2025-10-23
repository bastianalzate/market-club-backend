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
            $orders = Order::with(['user', 'orderItems.product', 'paymentTransactions'])
                ->where('created_at', '>=', $startDate)
                ->orderBy('created_at', 'desc')
                ->get();

            // Crear nombre del archivo
            $filename = 'ventas_' . $period . '_' . now()->format('Y-m-d_His') . '.csv';

            // Crear el contenido del CSV
            $csvContent = "\xEF\xBB\xBF"; // BOM para UTF-8
            
            // Encabezado del reporte
            $csvContent .= "REPORTE DE VENTAS - MARKET CLUB\n";
            $csvContent .= "Período: " . str_replace('_', ' ', $period) . "\n";
            $csvContent .= "Generado: " . now()->format('d/m/Y H:i:s') . "\n";
            $csvContent .= "\n";

            // Encabezados de las columnas
            $csvContent .= "# DE ORDEN,CLIENTE,MONTO,PRODUCTOS,ESTADO\n";
            
            // Agregar datos de las órdenes
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
                
                // Escapar comas y comillas en los datos
                $orderNumber = str_replace('"', '""', $order->order_number);
                $clientName = str_replace('"', '""', $order->user->name ?? 'Cliente no registrado');
                $amount = number_format($order->total_amount, 2);
                $productsStr = str_replace('"', '""', $products);
                $statusStr = str_replace('"', '""', $status);
                
                $csvContent .= "\"{$orderNumber}\",\"{$clientName}\",\"{$amount}\",\"{$productsStr}\",\"{$statusStr}\"\n";
            }

            // Retornar respuesta con el archivo CSV
            return response($csvContent, 200)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            \Log::error('Error exportando CSV: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar el reporte: ' . $e->getMessage());
        }
    }
}
