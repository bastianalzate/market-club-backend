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
}
