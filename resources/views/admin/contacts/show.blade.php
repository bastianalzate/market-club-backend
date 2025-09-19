@extends('admin.layouts.app')

@section('title', 'Detalle de Contacto')
@section('page-title', 'Detalle de Contacto')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header del Contacto -->
        <div class="bg-white border border-gray-200 rounded-xl mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span
                                    class="text-indigo-600 text-2xl font-medium">{{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $contact->full_name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">Contacto recibido el
                                {{ $contact->created_at->format('d/m/Y H:i') }}</p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $contact->status_color }}">
                                    {{ $contact->status_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.contacts.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Información del Contacto -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Mensaje -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Mensaje</h4>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            <p class="text-gray-900 whitespace-pre-line">{{ $contact->message }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notas Administrativas -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Notas Administrativas</h4>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.contacts.update', $contact) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Notas
                                    internas</label>
                                <textarea name="admin_notes" id="admin_notes" rows="4"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Agregar notas internas sobre este contacto...">{{ old('admin_notes', $contact->admin_notes) }}</textarea>
                                @error('admin_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select name="status" id="status"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="new" {{ $contact->status == 'new' ? 'selected' : '' }}>Nuevo</option>
                                    <option value="in_progress" {{ $contact->status == 'in_progress' ? 'selected' : '' }}>
                                        En Progreso</option>
                                    <option value="resolved" {{ $contact->status == 'resolved' ? 'selected' : '' }}>
                                        Resuelto</option>
                                    <option value="closed" {{ $contact->status == 'closed' ? 'selected' : '' }}>Cerrado
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="space-y-6">
                <!-- Datos Personales -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Información de Contacto</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Nombre</span>
                            <span class="text-sm font-medium text-gray-900">{{ $contact->first_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Apellidos</span>
                            <span class="text-sm font-medium text-gray-900">{{ $contact->last_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Email</span>
                            <a href="mailto:{{ $contact->email }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                {{ $contact->email }}
                            </a>
                        </div>
                        @if ($contact->phone)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Teléfono</span>
                                <a href="tel:{{ $contact->phone }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ $contact->phone }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estado y Seguimiento -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Seguimiento</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Estado Actual</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contact->status_color }}">
                                {{ $contact->status_text }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Fecha de Recepción</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($contact->resolved_at)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Fecha de Resolución</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $contact->resolved_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if ($contact->resolvedBy)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Resuelto por</span>
                                <span class="text-sm font-medium text-gray-900">{{ $contact->resolvedBy->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Referencia -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Referencia</h4>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-2xl font-mono font-bold text-gray-900">
                                CONTACT-{{ str_pad($contact->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">ID de referencia del contacto</p>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900">Acciones Rápidas</h4>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="mailto:{{ $contact->email }}?subject=Re: Tu consulta - CONTACT-{{ str_pad($contact->id, 6, '0', STR_PAD_LEFT) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Responder por Email
                        </a>

                        @if ($contact->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}?text=Hola {{ $contact->first_name }}, te contactamos desde Market Club respecto a tu consulta."
                                target="_blank"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488z" />
                                </svg>
                                Contactar por WhatsApp
                            </a>
                        @endif

                        @if ($contact->status !== 'resolved')
                            <form method="POST" action="{{ route('admin.contacts.update', $contact) }}" class="w-full">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="resolved">
                                <input type="hidden" name="admin_notes" value="{{ $contact->admin_notes }}">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Marcar como Resuelto
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                            onsubmit="return confirm('¿Estás seguro de eliminar este contacto? Esta acción no se puede deshacer.')"
                            class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Eliminar Contacto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
