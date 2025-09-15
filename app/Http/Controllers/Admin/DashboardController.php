<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'today_sales' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->count(),
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

        // Ventas por mes (últimos 6 meses) - Compatible con SQLite y MySQL
        $dbDriver = config('database.default');
        if ($dbDriver === 'sqlite') {
            $monthly_sales = Order::selectRaw('strftime("%Y-%m", created_at) as month, SUM(total_amount) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->where('payment_status', 'paid')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthly_sales = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->where('payment_status', 'paid')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return view('admin.dashboard.index', compact('stats', 'recent_orders', 'top_products', 'monthly_sales'));
    }
}
