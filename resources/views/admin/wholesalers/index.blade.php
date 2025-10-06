@extends('admin.layouts.app')

@section('title', 'Mayoristas')
@section('page-title', 'Gestión de Mayoristas')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mayoristas</h1>
            <p class="mt-1 text-sm text-gray-500">Gestiona todos los mayoristas registrados en tu tienda</p>
        </div>
        
    </div>

    <!-- Filtros -->
    <div class="bg-white border border-gray-200 rounded-xl mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.wholesalers.index') }}"
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" id="search" name="search"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Nombre del negocio, contacto o email..." value="{{ request('search') }}">
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">País</label>
                    <select id="country" name="country"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Todos los países</option>
                        <option value="Colombia" {{ request('country') == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                        <option value="México" {{ request('country') == 'México' ? 'selected' : '' }}>México</option>
                        <option value="Argentina" {{ request('country') == 'Argentina' ? 'selected' : '' }}>Argentina
                        </option>
                        <option value="Chile" {{ request('country') == 'Chile' ? 'selected' : '' }}>Chile</option>
                        <option value="Perú" {{ request('country') == 'Perú' ? 'selected' : '' }}>Perú</option>
                        <option value="España" {{ request('country') == 'España' ? 'selected' : '' }}>España</option>
                        <option value="Estados Unidos" {{ request('country') == 'Estados Unidos' ? 'selected' : '' }}>
                            Estados Unidos</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status" name="status"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Todos los estados</option>
                        <option value="enabled" {{ request('status') == 'enabled' ? 'selected' : '' }}>Habilitado</option>
                        <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>No Habilitado
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Mayoristas -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Lista de Mayoristas</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $wholesalers->total() }} mayoristas encontrados</p>
        </div>

        @if ($wholesalers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mayorista</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ubicación
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Activo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha
                                de Registro
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($wholesalers as $wholesaler)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span
                                                    class="text-indigo-600 text-sm font-medium">{{ strtoupper(substr($wholesaler->name, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $wholesaler->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">ID: {{ $wholesaler->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <div>
                                            <div class="text-sm text-gray-900">{{ $wholesaler->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $wholesaler->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($wholesaler->phone)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                            <div class="text-sm text-gray-900">{{ $wholesaler->phone }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="text-sm text-gray-900">{{ $wholesaler->country ?? 'Sin ubicación' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($wholesaler->is_active) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif"
                                        data-wholesaler-id="{{ $wholesaler->id }}">
                                        @if ($wholesaler->is_active)
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Habilitado
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            No Habilitado
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer wholesaler-toggle"
                                            data-wholesaler-id="{{ $wholesaler->id }}"
                                            {{ $wholesaler->is_active ? 'checked' : '' }}>
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">
                                            {{ $wholesaler->is_active ? 'Sí' : 'No' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <div>
                                            <div>{{ $wholesaler->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ $wholesaler->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if (!$wholesaler->is_active)
                                            <form action="{{ route('admin.wholesalers.approve', $wholesaler) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900"
                                                    title="Aprobar mayorista">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.wholesalers.show', $wholesaler) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Ver detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.wholesalers.edit', $wholesaler) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar mayorista">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.wholesalers.destroy', $wholesaler) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('¿Estás seguro de que quieres eliminar este mayorista?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                title="Eliminar mayorista">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($wholesalers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $wholesalers->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay mayoristas</h3>
                <p class="mt-1 text-sm text-gray-500">No se encontraron mayoristas con los filtros aplicados.</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el toggle de estado de mayoristas
            document.querySelectorAll('.wholesaler-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const wholesalerId = this.dataset.wholesalerId;
                    const isActive = this.checked;

                    // Deshabilitar el toggle mientras se procesa
                    this.disabled = true;

                    // Enviar petición AJAX
                    fetch(`/admin/wholesalers/${wholesalerId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostrar mensaje de éxito
                                showNotification(data.message, 'success');

                                // Actualizar el texto del estado
                                const statusText = this.parentElement.querySelector('span');
                                statusText.textContent = data.status === 'enabled' ? 'Sí' :
                                    'No';

                                // Actualizar el badge de estado en la tabla
                                updateStatusBadge(wholesalerId, data.status);
                            } else {
                                // Revertir el toggle si hay error
                                this.checked = !isActive;
                                showNotification('Error al actualizar el estado', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Revertir el toggle si hay error
                            this.checked = !isActive;
                            showNotification('Error al actualizar el estado', 'error');
                        })
                        .finally(() => {
                            // Rehabilitar el toggle
                            this.disabled = false;
                        });
                });
            });
        });

        function updateStatusBadge(wholesalerId, newStatus) {
            // Buscar específicamente el badge de estado por su clase y data-attribute
            const statusBadge = document.querySelector(`.status-badge[data-wholesaler-id="${wholesalerId}"]`);

            if (statusBadge) {
                // Remover clases de estado actual
                statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800',
                    'bg-yellow-100', 'text-yellow-800');

                // Agregar clases del nuevo estado
                if (newStatus === 'enabled') {
                    statusBadge.classList.add('bg-green-100', 'text-green-800');
                    statusBadge.innerHTML = `
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Habilitado
                    `;
                } else {
                    statusBadge.classList.add('bg-red-100', 'text-red-800');
                    statusBadge.innerHTML = `
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        No Habilitado
                    `;
                }
            }
        }

        function showNotification(message, type) {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
            notification.textContent = message;

            // Agregar al DOM
            document.body.appendChild(notification);

            // Remover después de 3 segundos
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
@endpush
