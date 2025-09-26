@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="md:items-center md:flex">
        <p class="text-base font-bold text-gray-900">Hola Administrador -</p>
        <p class="mt-1 text-base font-medium text-gray-500 md:mt-0 md:ml-2">aquí tienes un resumen de tu tienda hoy</p>
    </div>

    <div class="mt-8 space-y-5 sm:space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Total Productos</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_products']) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Total Órdenes</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_orders']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Ingresos Totales</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">
                                ${{ number_format($stats['total_revenue'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Usuarios</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Órdenes Pendientes</p>
                            <p class="text-2xl font-bold text-orange-600 mt-2">{{ number_format($stats['pending_orders']) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Stock Bajo</p>
                            <p class="text-2xl font-bold text-red-600 mt-2">
                                {{ number_format($stats['low_stock_products']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wider text-gray-500 uppercase">Categorías</p>
                            <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($stats['total_categories']) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-5 sm:gap-6 lg:grid-cols-6">
            <!-- Sales Report Chart -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-xl lg:col-span-4">
                <div class="px-4 pt-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between">
                        <p class="text-base font-bold text-gray-900 lg:order-1">Reporte de Ventas</p>

                        <button type="button" id="exportCSVBtn"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm lg:order-2 2xl:order-3 md:order-last hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg id="exportIcon" class="w-4 h-4 mr-1 -ml-1" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <svg id="loadingSpinner" class="w-4 h-4 mr-1 -ml-1 animate-spin hidden"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span id="exportText">Exportar CSV</span>
                            <span id="loadingText" class="hidden">Generando...</span>
                        </button>

                        <nav
                            class="flex items-center justify-center mt-4 space-x-1 2xl:order-2 lg:order-3 md:mt-0 lg:mt-4 sm:space-x-2 2xl:mt-0">
                            <button type="button" data-period="12_months"
                                class="period-btn px-2 py-2 text-xs font-bold text-gray-900 transition-all border border-gray-900 rounded-lg sm:px-4 hover:bg-gray-100 duration-200">
                                12 Meses </button>
                            <button type="button" data-period="6_months"
                                class="period-btn px-2 py-2 text-xs font-bold text-gray-500 transition-all border border-transparent rounded-lg sm:px-4 hover:bg-gray-100 duration-200">
                                6 Meses </button>
                            <button type="button" data-period="30_days"
                                class="period-btn px-2 py-2 text-xs font-bold text-gray-500 transition-all border border-transparent rounded-lg sm:px-4 hover:bg-gray-100 duration-200">
                                30 Días </button>
                            <button type="button" data-period="7_days"
                                class="period-btn px-2 py-2 text-xs font-bold text-gray-500 transition-all border border-transparent rounded-lg sm:px-4 hover:bg-gray-100 duration-200">
                                7 Días </button>
                        </nav>
                    </div>

                    <div id="salesChart" class="mt-4"></div>
                </div>
            </div>

            <!-- Subscription Analytics -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-xl lg:col-span-2">
                <div class="px-4 py-5 sm:p-6">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-base font-bold text-gray-900">Suscripciones</p>
                            <p class="mt-1 text-sm font-medium text-gray-500">Estado de los planes de suscripción</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('admin.orders.index') }}" title=""
                                class="inline-flex items-center text-xs font-semibold tracking-widest text-gray-500 uppercase hover:text-gray-900">
                                Ver órdenes
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 space-y-6">
                        <div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">Suscripciones Activas</p>
                                <p class="text-sm font-bold text-green-600">
                                    {{ number_format($stats['active_subscriptions']) }}</p>
                            </div>
                            <div class="mt-2 bg-gray-200 h-1.5 rounded-full relative">
                                @php
                                    $activePercentage =
                                        $stats['total_subscriptions'] > 0
                                            ? ($stats['active_subscriptions'] / $stats['total_subscriptions']) * 100
                                            : 0;
                                @endphp
                                <div class="absolute inset-y-0 left-0 bg-green-600 rounded-full"
                                    style="width: {{ $activePercentage }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">de {{ number_format($stats['total_subscriptions']) }}
                                total</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">Ingresos por Suscripciones</p>
                                <p class="text-sm font-bold text-blue-600">
                                    ${{ number_format($stats['subscription_revenue'], 0, ',', '.') }}</p>
                            </div>
                            <div class="mt-2 bg-gray-200 h-1.5 rounded-full relative">
                                @php
                                    $revenuePercentage =
                                        $stats['total_revenue'] > 0
                                            ? ($stats['subscription_revenue'] / $stats['total_revenue']) * 100
                                            : 0;
                                @endphp
                                <div class="absolute inset-y-0 left-0 bg-blue-600 rounded-full"
                                    style="width: {{ min($revenuePercentage, 100) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">del total de ingresos</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">Por Vencer (7 días)</p>
                                <p class="text-sm font-bold text-orange-600">{{ number_format($stats['expiring_soon']) }}
                                </p>
                            </div>
                            @if ($stats['expiring_soon'] > 0)
                                <div class="mt-2 bg-gray-200 h-1.5 rounded-full relative">
                                    @php
                                        $expiringPercentage =
                                            $stats['active_subscriptions'] > 0
                                                ? ($stats['expiring_soon'] / $stats['active_subscriptions']) * 100
                                                : 0;
                                    @endphp
                                    <div class="absolute inset-y-0 left-0 bg-orange-600 rounded-full"
                                        style="width: {{ min($expiringPercentage, 100) }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-orange-600">Requieren atención</p>
                            @else
                                <div class="mt-2 bg-gray-200 h-1.5 rounded-full relative">
                                    <div class="absolute inset-y-0 left-0 bg-green-600 rounded-full w-full"></div>
                                </div>
                                <p class="mt-1 text-xs text-green-600">Todas al día</p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">Promedio por Suscripción</p>
                                <p class="text-sm font-bold text-purple-600">
                                    @php
                                        $avgSubscription =
                                            $stats['active_subscriptions'] > 0
                                                ? $stats['subscription_revenue'] / $stats['active_subscriptions']
                                                : 0;
                                    @endphp
                                    ${{ number_format($avgSubscription, 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Valor promedio mensual</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 gap-5 sm:gap-6 lg:grid-cols-6">
            <!-- Recent Orders -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-xl lg:col-span-4">
                <div class="px-4 py-5 sm:p-6">
                    <div class="sm:flex sm:items-start sm:justify-between">
                        <div>
                            <p class="text-base font-bold text-gray-900">Órdenes Recientes</p>
                            <p class="mt-1 text-sm font-medium text-gray-500">Últimas transacciones de tu tienda</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('admin.orders.index') }}" title=""
                                class="inline-flex items-center text-xs font-semibold tracking-widest text-gray-500 uppercase hover:text-gray-900">
                                Ver todas las órdenes
                                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($recent_orders as $order)
                        <div class="grid grid-cols-3 py-4 gap-y-4 lg:gap-0 lg:grid-cols-6">
                            <div class="col-span-2 px-4 lg:py-4 sm:px-6 lg:col-span-1">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-900',
                                        'processing' => 'bg-blue-100 text-blue-900',
                                        'shipped' => 'bg-purple-100 text-purple-900',
                                        'delivered' => 'bg-green-100 text-green-900',
                                        'cancelled' => 'bg-red-100 text-red-900',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'processing' => 'Procesando',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregado',
                                        'cancelled' => 'Cancelado',
                                    ];
                                @endphp
                                <span
                                    class="text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-900' }} rounded-full inline-flex items-center px-2.5 py-1">
                                    <svg class="-ml-1 mr-1.5 h-2.5 w-2.5 {{ $order->status === 'delivered' ? 'text-green-500' : ($order->status === 'cancelled' ? 'text-red-500' : 'text-yellow-500') }}"
                                        fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"></circle>
                                    </svg>
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="px-4 text-right lg:py-4 sm:px-6 lg:order-last">
                                <button type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-400 transition-all duration-200 bg-white rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                        </path>
                                    </svg>
                                </button>
                            </div>

                            <div class="px-4 lg:py-4 sm:px-6 lg:col-span-2">
                                <p class="text-sm font-bold text-gray-900">Orden #{{ $order->order_number }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-500">{{ $order->user->name ?? 'Cliente' }}
                                </p>
                            </div>

                            <div class="px-4 lg:py-4 sm:px-6">
                                <p class="text-sm font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}
                                </p>
                                <p class="mt-1 text-sm font-medium text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}</p>
                            </div>

                            <div class="px-4 lg:py-4 sm:px-6">
                                <p class="mt-1 text-sm font-medium text-gray-500">{{ $order->payment_method ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-gray-500">No hay órdenes recientes</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Products -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-xl lg:col-span-2">
                <div class="px-4 py-5 sm:p-6">
                    <div>
                        <p class="text-base font-bold text-gray-900">Productos Populares</p>
                        <p class="mt-1 text-sm font-medium text-gray-500">Los más vendidos esta semana</p>
                    </div>

                    <div class="mt-8 space-y-6">
                        @forelse($top_products as $product)
                            <div class="flex items-center justify-between space-x-5">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 ml-4">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="mt-1 text-sm font-medium text-gray-500">
                                            ${{ number_format($product->current_price, 2) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->total_sold ?? 0 }} vendidos
                                    </p>
                                    <p class="mt-1 text-sm font-medium text-gray-500">Stock:
                                        {{ $product->stock_quantity }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500">No hay datos de productos</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('admin.products.index') }}" title=""
                            class="inline-flex items-center text-xs font-semibold tracking-widest text-gray-500 uppercase hover:text-gray-900">
                            Ver todos los productos
                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Variables globales
        let currentPeriod = '12_months';

        // Sales Chart
        var salesChartOptions = {
            chart: {
                type: 'area',
                height: 260,
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            series: [{
                    name: 'Ventas',
                    data: @json($monthly_sales->pluck('total')),
                },
                {
                    name: 'Órdenes',
                    data: @json(
                        $monthly_sales->pluck('total')->map(function ($item) {
                            return $item * 0.6;
                        })),
                },
            ],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: true,
                curve: 'smooth',
                lineCap: 'butt',
                colors: undefined,
                width: 2,
            },
            grid: {
                row: {
                    opacity: 0,
                },
            },
            xaxis: {
                categories: @json($monthly_sales->pluck('month')),
            },
            yaxis: {
                show: false,
            },
            fill: {
                type: 'solid',
                opacity: [0.05, 0],
            },
            colors: ['#4F46E5', '#818CF8'],
            legend: {
                position: 'bottom',
                markers: {
                    radius: 12,
                    offsetX: -4,
                },
                itemMargin: {
                    horizontal: 12,
                    vertical: 20,
                },
            },
        }

        var salesChart = new ApexCharts(document.querySelector('#salesChart'), salesChartOptions)
        salesChart.render()

        // Manejar selección de período
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Actualizar estado visual
                document.querySelectorAll('.period-btn').forEach(b => {
                    b.classList.remove('text-gray-900', 'border-gray-900');
                    b.classList.add('text-gray-500', 'border-transparent');
                });

                this.classList.remove('text-gray-500', 'border-transparent');
                this.classList.add('text-gray-900', 'border-gray-900');

                // Actualizar período actual
                currentPeriod = this.dataset.period;
            });
        });

        // Manejar exportación CSV
        document.getElementById('exportCSVBtn').addEventListener('click', function() {
            const btn = this;
            const exportIcon = document.getElementById('exportIcon');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const exportText = document.getElementById('exportText');
            const loadingText = document.getElementById('loadingText');

            // Activar estado de loading
            btn.disabled = true;
            exportIcon.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            exportText.classList.add('hidden');
            loadingText.classList.remove('hidden');

            // Crear URL con parámetro de período
            const exportUrl = '{{ route('admin.dashboard.export-sales') }}?period=' + currentPeriod;

            // Usar fetch para manejar la descarga y errores
            fetch(exportUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/csv, application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Verificar si la respuesta es JSON (error) o CSV
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error al generar el archivo');
                        });
                    }

                    return response.blob();
                })
                .then(blob => {
                    // Crear enlace de descarga
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'ordenes_' + currentPeriod + '_{{ now()->format('Y-m-d') }}.csv';

                    // Simular click para iniciar descarga
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Limpiar URL
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Error en exportación:', error);
                    alert('Error al exportar el archivo: ' + error.message);
                })
                .finally(() => {
                    // Desactivar estado de loading
                    btn.disabled = false;
                    exportIcon.classList.remove('hidden');
                    loadingSpinner.classList.add('hidden');
                    exportText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                });
        });
    </script>
@endpush
