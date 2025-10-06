@extends('admin.layouts.app')

@section('title', 'Detalles del Mayorista')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-4">
                                <li>
                                    <a href="{{ route('admin.wholesalers.index') }}"
                                        class="text-gray-500 hover:text-gray-700">
                                        Mayoristas
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span
                                            class="ml-4 text-sm font-medium text-gray-500">{{ $wholesaler->business_name }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $wholesaler->name }}</h1>
                        <p class="mt-2 text-sm text-gray-700">{{ $wholesaler->email }}</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <div class="flex space-x-3">
                            @if($wholesaler->wholesaler_document_path)
                                <button onclick="openFilesModal()"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Ver Archivos
                                </button>
                            @endif
                            <a href="{{ route('admin.wholesalers.edit', $wholesaler) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            @if (!$wholesaler->is_active)
                                <form action="{{ route('admin.wholesalers.approve', $wholesaler) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Aprobar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Principal -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Información del Negocio -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Mayorista</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($wholesaler->is_active) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            @if ($wholesaler->is_active)
                                                Activo
                                            @else
                                                Inactivo
                                            @endif
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de Registro</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $wholesaler->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                @if ($wholesaler->phone)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->phone }}</dd>
                                    </div>
                                @endif
                                @if ($wholesaler->nit)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">NIT</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->nit }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Información de Ubicación -->
                    @if ($wholesaler->address || $wholesaler->country)
                        <div class="mt-6 bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Ubicación</h3>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    @if ($wholesaler->address)
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ is_array($wholesaler->address) ? implode(', ', $wholesaler->address) : $wholesaler->address }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">País</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->country }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Información Comercial -->
                <div>
                    @if ($wholesaler->wholesaler_document_path)
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Documentos</h3>
                                <div class="flex items-center space-x-3">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $wholesaler->wholesaler_document_original_name }}</p>
                                        <p class="text-xs text-gray-500">Documento subido</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Archivos -->
    <div id="filesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header del Modal -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Archivos del Mayorista</h3>
                    <button onclick="closeFilesModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div class="space-y-4">
                    @if($wholesaler->wholesaler_document_path)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $wholesaler->wholesaler_document_original_name }}</h4>
                                    <p class="text-xs text-gray-500">Documento subido</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.wholesalers.serve-file', ['wholesaler' => $wholesaler->id, 'filename' => basename($wholesaler->wholesaler_document_path)]) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.wholesalers.serve-file', ['wholesaler' => $wholesaler->id, 'filename' => basename($wholesaler->wholesaler_document_path)]) }}" 
                                       download="{{ $wholesaler->wholesaler_document_original_name }}"
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay archivos</h3>
                            <p class="mt-1 text-sm text-gray-500">Este mayorista no ha subido ningún documento.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer del Modal -->
                <div class="flex justify-end mt-6">
                    <button onclick="closeFilesModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openFilesModal() {
            document.getElementById('filesModal').classList.remove('hidden');
        }

        function closeFilesModal() {
            document.getElementById('filesModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('filesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilesModal();
            }
        });
    </script>
@endsection
