@extends('admin.layouts.app')

@section('title', 'Editar Orden')
@section('page-title', 'Editar Orden')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Órdenes
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.show', $order) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Orden
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario principal -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Editar Orden #{{ $order->order_number }}</h3>
                        <p class="mt-1 text-sm text-gray-500">Actualiza el estado y la información de la orden</p>
                    </div>

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Información de la orden -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado de la
                                    Orden *</label>
                                <select name="status" id="status" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                                    <option value="pending"
                                        {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pendiente
                                    </option>
                                    <option value="processing"
                                        {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Procesando
                                    </option>
                                    <option value="shipped"
                                        {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Enviado</option>
                                    <option value="delivered"
                                        {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Entregado
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelado
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Estado del
                                    Pago *</label>
                                <select name="payment_status" id="payment_status" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_status') border-red-300 @enderror">
                                    <option value="pending"
                                        {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>
                                        Pendiente</option>
                                    <option value="paid"
                                        {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>
                                        Pagado</option>
                                    <option value="failed"
                                        {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>
                                        Fallido</option>
                                    <option value="refunded"
                                        {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>
                                        Reembolsado</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notas -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas
                                Internas</label>
                            <textarea name="notes" id="notes" rows="4"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror"
                                placeholder="Agrega notas internas sobre esta orden...">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.orders.show', $order) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                Actualizar Orden
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Información de la Orden -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información de la Orden</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número de Orden</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $order->order_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                                <dd class="text-sm text-gray-900">{{ $order->user?->name ?? 'Usuario no encontrado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $order->user?->email ?? 'Email no disponible' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                    ${{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                                <dd class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Productos de la Orden -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach ($order->orderItems as $item)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if ($item->is_gift)
                                            {{-- Ícono para regalos personalizados --}}
                                            <div
                                                class="h-10 w-10 rounded-lg bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center border border-gray-200">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                                                    </path>
                                                </svg>
                                            </div>
                                        @elseif ($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                alt="{{ $item->product->name }}"
                                                class="h-10 w-10 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div
                                                class="h-10 w-10 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            @if ($item->is_gift)
                                                {{ $item->gift_data['name'] ?? 'Regalo Personalizado' }}
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-1">
                                                    Regalo
                                                </span>
                                            @else
                                                {{ $item->product?->name ?? 'Producto no disponible' }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Cantidad: {{ $item->quantity }}
                                            @if ($item->is_gift && isset($item->gift_data['beers']))
                                                - {{ count($item->gift_data['beers']) }} cervezas
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-900">
                                        ${{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Estados Actuales -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Estados Actuales</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Estado de la Orden</label>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                                        @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800 @endif">
                                        @switch($order->status)
                                            @case('pending')
                                                Pendiente
                                            @break

                                            @case('processing')
                                                Procesando
                                            @break

                                            @case('shipped')
                                                Enviado
                                            @break

                                            @case('delivered')
                                                Entregado
                                            @break

                                            @case('cancelled')
                                                Cancelado
                                            @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Estado del Pago</label>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                        @elseif($order->payment_status == 'refunded') bg-gray-100 text-gray-800 @endif">
                                        @switch($order->payment_status)
                                            @case('pending')
                                                Pendiente
                                            @break

                                            @case('paid')
                                                Pagado
                                            @break

                                            @case('failed')
                                                Fallido
                                            @break

                                            @case('refunded')
                                                Reembolsado
                                            @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación del formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const statusField = document.getElementById('status');
                const paymentStatusField = document.getElementById('payment_status');
                let isValid = true;

                if (!statusField.value) {
                    statusField.classList.add('border-red-300');
                    isValid = false;
                } else {
                    statusField.classList.remove('border-red-300');
                }

                if (!paymentStatusField.value) {
                    paymentStatusField.classList.add('border-red-300');
                    isValid = false;
                } else {
                    paymentStatusField.classList.remove('border-red-300');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos obligatorios.');
                }
            });

            // Actualizar estados en tiempo real en el sidebar
            const statusSelect = document.getElementById('status');
            const paymentStatusSelect = document.getElementById('payment_status');

            // Función para actualizar el badge de estado
            function updateStatusBadge(selectElement, badgeClass) {
                const value = selectElement.value;
                const badge = document.querySelector(badgeClass);
                if (badge) {
                    // Remover todas las clases de color
                    badge.className = badge.className.replace(/bg-\w+-100 text-\w+-800/g, '');

                    // Agregar la clase correspondiente
                    let colorClass = '';
                    switch (value) {
                        case 'pending':
                            colorClass = 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'processing':
                        case 'paid':
                            colorClass = 'bg-blue-100 text-blue-800';
                            break;
                        case 'shipped':
                            colorClass = 'bg-indigo-100 text-indigo-800';
                            break;
                        case 'delivered':
                            colorClass = 'bg-green-100 text-green-800';
                            break;
                        case 'cancelled':
                        case 'failed':
                            colorClass = 'bg-red-100 text-red-800';
                            break;
                        case 'refunded':
                            colorClass = 'bg-gray-100 text-gray-800';
                            break;
                    }
                    badge.className += ' ' + colorClass;
                }
            }

            // Escuchar cambios en los selects
            statusSelect.addEventListener('change', function() {
                updateStatusBadge(this,
                    '.bg-yellow-100, .bg-blue-100, .bg-indigo-100, .bg-green-100, .bg-red-100');
            });

            paymentStatusSelect.addEventListener('change', function() {
                updateStatusBadge(this, '.bg-yellow-100, .bg-green-100, .bg-red-100, .bg-gray-100');
            });
        });
    </script>
@endsection
