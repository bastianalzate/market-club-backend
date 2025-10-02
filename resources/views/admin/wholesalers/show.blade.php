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
                        <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $wholesaler->business_name }}</h1>
                        <p class="mt-2 text-sm text-gray-700">{{ $wholesaler->business_description }}</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.wholesalers.edit', $wholesaler) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            @if ($wholesaler->status === 'disabled')
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Negocio</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre del Negocio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->business_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo de Negocio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $wholesaler->business_type)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($wholesaler->status === 'enabled') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            @if ($wholesaler->status === 'enabled')
                                                Habilitado
                                            @else
                                                No Habilitado
                                            @endif
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de Registro</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $wholesaler->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                @if ($wholesaler->approved_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha de Aprobación</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $wholesaler->approved_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                @endif
                                @if ($wholesaler->approver)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Aprobado por</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->approver->name }}</dd>
                                    </div>
                                @endif
                            </dl>

                            @if ($wholesaler->business_description)
                                <div class="mt-6">
                                    <dt class="text-sm font-medium text-gray-500">Descripción del Negocio</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->business_description }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre de Contacto</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->contact_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <a href="mailto:{{ $wholesaler->email }}"
                                            class="text-indigo-600 hover:text-indigo-500">
                                            {{ $wholesaler->email }}
                                        </a>
                                    </dd>
                                </div>
                                @if ($wholesaler->phone)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->phone }}</dd>
                                    </div>
                                @endif
                                @if ($wholesaler->tax_id)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">NIT/RUT</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->tax_id }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Información de Ubicación -->
                    @if ($wholesaler->address || $wholesaler->city)
                        <div class="mt-6 bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Ubicación</h3>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    @if ($wholesaler->address)
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->address }}</dd>
                                        </div>
                                    @endif
                                    @if ($wholesaler->city)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->city }}</dd>
                                        </div>
                                    @endif
                                    @if ($wholesaler->state)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Departamento/Estado</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->state }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">País</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->country }}</dd>
                                    </div>
                                    @if ($wholesaler->postal_code)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Código Postal</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $wholesaler->postal_code }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Información Comercial -->
                <div>

                    @if ($wholesaler->notes)
                        <div class="mt-6 bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Notas</h3>
                                <p class="text-sm text-gray-900">{{ $wholesaler->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
