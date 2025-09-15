@extends('admin.layouts.app')

@section('title', 'Detalle de Orden')
@section('page-title', 'Detalle de Orden')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header de la Orden -->
        <div class="bg-white border border-gray-200 rounded-xl mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Orden #{{ $order->order_number }}</h3>
                        <p class="mt-1 text-sm text-gray-500">Creada el {{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-purple-100 text-purple-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
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
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                        <a href="{{ route('admin.orders.edit', $order) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Información del Cliente -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detalles del Cliente -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Información del Cliente</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <p class="text-sm text-gray-900">{{ $order->user?->name ?? 'Usuario no encontrado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-sm text-gray-900">{{ $order->user?->email ?? 'Email no disponible' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <p class="text-sm text-gray-900">{{ $order->user?->phone ?? 'Teléfono no disponible' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                                <p class="text-sm text-gray-900">{{ $order->user?->country ?? 'País no disponible' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Productos</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if ($item->product->image)
                                                        <img class="h-10 w-10 rounded-lg object-cover"
                                                            src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}">
                                                    @else
                                                        <div
                                                            class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->product->name }}</div>
                                                    <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Direcciones -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Dirección de Envío -->
                    <div class="bg-white border border-gray-200 rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-lg font-medium text-gray-900">Dirección de Envío</h4>
                        </div>
                        <div class="p-6">
                            @php
                                $shippingAddress = is_string($order->shipping_address)
                                    ? json_decode($order->shipping_address, true)
                                    : $order->shipping_address;
                            @endphp
                            @if ($shippingAddress)
                                <div class="text-sm text-gray-900">
                                    <p class="font-medium">{{ $shippingAddress['name'] ?? 'N/A' }}</p>
                                    <p>{{ $shippingAddress['address'] ?? 'N/A' }}</p>
                                    <p>{{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }}
                                    </p>
                                    <p>{{ $shippingAddress['postal_code'] ?? 'N/A' }},
                                        {{ $shippingAddress['country'] ?? 'N/A' }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No disponible</p>
                            @endif
                        </div>
                    </div>

                    <!-- Dirección de Facturación -->
                    <div class="bg-white border border-gray-200 rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-lg font-medium text-gray-900">Dirección de Facturación</h4>
                        </div>
                        <div class="p-6">
                            @php
                                $billingAddress = is_string($order->billing_address)
                                    ? json_decode($order->billing_address, true)
                                    : $order->billing_address;
                            @endphp
                            @if ($billingAddress)
                                <div class="text-sm text-gray-900">
                                    <p class="font-medium">{{ $billingAddress['name'] ?? 'N/A' }}</p>
                                    <p>{{ $billingAddress['address'] ?? 'N/A' }}</p>
                                    <p>{{ $billingAddress['city'] ?? 'N/A' }}, {{ $billingAddress['state'] ?? 'N/A' }}</p>
                                    <p>{{ $billingAddress['postal_code'] ?? 'N/A' }},
                                        {{ $billingAddress['country'] ?? 'N/A' }}</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No disponible</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de la Orden -->
            <div class="space-y-6">
                <!-- Resumen de Pago -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Resumen de Pago</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Impuestos</span>
                            <span class="text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Envío</span>
                            <span class="text-gray-900">${{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-lg font-medium">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Pago -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Información de Pago</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Pago</label>
                            @php
                                $paymentColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <p class="text-sm text-gray-900">{{ $order->payment_method ?? 'N/A' }}</p>
                        </div>
                        @if ($order->payment_reference)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Referencia de Pago</label>
                                <p class="text-sm text-gray-900">{{ $order->payment_reference }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notas -->
                @if ($order->notes)
                    <div class="bg-white border border-gray-200 rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-lg font-medium text-gray-900">Notas</h4>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-900">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
