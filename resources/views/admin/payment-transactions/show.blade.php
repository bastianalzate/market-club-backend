@extends('admin.layouts.app')

@section('title', 'Detalle de Transacción')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.payment-transactions.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Transacciones
                </a>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detalle de Transacción</h1>
                <p class="text-gray-600">{{ $paymentTransaction->reference }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información de la Transacción -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información de la Transacción</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referencia</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->reference }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID Wompi</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $paymentTransaction->wompi_transaction_id }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->payment_method_label }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                ${{ number_format($paymentTransaction->amount, 0, ',', '.') }} COP</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            @php
                                $statusColors = [
                                    'APPROVED' => 'bg-green-100 text-green-800',
                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                    'DECLINED' => 'bg-red-100 text-red-800',
                                    'VOIDED' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$paymentTransaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $paymentTransaction->status_label }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $paymentTransaction->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @if ($paymentTransaction->processed_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Procesamiento</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $paymentTransaction->processed_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Cliente</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->order->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->order->user->email }}</p>
                        </div>
                        @if ($paymentTransaction->order->user->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->order->user->phone }}</p>
                            </div>
                        @endif
                        @if ($paymentTransaction->order->user->country)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">País</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->order->user->country }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información de la Orden -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información de la Orden</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número de Orden</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->order->order_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado de la Orden</label>
                            @php
                                $orderStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $orderStatusColors[$paymentTransaction->order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($paymentTransaction->order->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado del Pago</label>
                            @php
                                $paymentStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusColors[$paymentTransaction->order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($paymentTransaction->order->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total de la Orden</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                ${{ number_format($paymentTransaction->order->total_amount, 0, ',', '.') }} COP</p>
                        </div>
                    </div>
                </div>

                <!-- Productos de la Orden -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Productos de la Orden</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Unit.</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($paymentTransaction->order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Acciones Rápidas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.orders.show', $paymentTransaction->order) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Ver Orden
                        </a>
                        <a href="{{ route('admin.users.show', $paymentTransaction->order->user) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Ver Cliente
                        </a>
                    </div>
                </div>

                <!-- Información Técnica -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Técnica</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Moneda</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->currency }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado Wompi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $paymentTransaction->wompi_status ?? 'N/A' }}</p>
                        </div>
                        @if ($paymentTransaction->payment_url)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">URL de Pago</label>
                                <a href="{{ $paymentTransaction->payment_url }}" target="_blank"
                                    class="mt-1 text-sm text-indigo-600 hover:text-indigo-900 break-all">
                                    {{ $paymentTransaction->payment_url }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Respuesta de Wompi -->
                @if ($paymentTransaction->wompi_response)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Respuesta de Wompi</h3>
                        <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded-md overflow-auto max-h-64">{{ json_encode($paymentTransaction->wompi_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
