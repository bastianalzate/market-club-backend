@extends('admin.layouts.app')

@section('title', 'Editar Mayorista')
@section('page-title', 'Editar Mayorista')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Información del Mayorista</h3>
                <p class="mt-1 text-sm text-gray-500">Actualiza la información del mayorista</p>
            </div>

            <form action="{{ route('admin.wholesalers.update', $wholesaler) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Negocio
                            *</label>
                        <input type="text" name="business_name" id="business_name"
                            value="{{ old('business_name', $wholesaler->business_name) }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('business_name') border-red-300 @enderror">
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Negocio
                            *</label>
                        <select name="business_type" id="business_type" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('business_type') border-red-300 @enderror">
                            <option value="">Seleccionar tipo</option>
                            <option value="restaurant"
                                {{ old('business_type', $wholesaler->business_type) == 'restaurant' ? 'selected' : '' }}>
                                Restaurante</option>
                            <option value="bar"
                                {{ old('business_type', $wholesaler->business_type) == 'bar' ? 'selected' : '' }}>
                                Bar</option>
                            <option value="retail_store"
                                {{ old('business_type', $wholesaler->business_type) == 'retail_store' ? 'selected' : '' }}>
                                Tienda</option>
                            <option value="distributor"
                                {{ old('business_type', $wholesaler->business_type) == 'distributor' ? 'selected' : '' }}>
                                Distribuidor</option>
                            <option value="other"
                                {{ old('business_type', $wholesaler->business_type) == 'other' ? 'selected' : '' }}>
                                Otro</option>
                        </select>
                        @error('business_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="business_description" class="block text-sm font-medium text-gray-700 mb-2">Descripción
                            del Negocio</label>
                        <textarea name="business_description" id="business_description" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('business_description') border-red-300 @enderror">{{ old('business_description', $wholesaler->business_description) }}</textarea>
                        @error('business_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Información de Contacto</h4>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de
                                Contacto *</label>
                            <input type="text" name="contact_name" id="contact_name"
                                value="{{ old('contact_name', $wholesaler->contact_name) }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('contact_name') border-red-300 @enderror">
                            @error('contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $wholesaler->email) }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="text" name="phone" id="phone"
                                value="{{ old('phone', $wholesaler->phone) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">NIT/RUT</label>
                            <input type="text" name="tax_id" id="tax_id"
                                value="{{ old('tax_id', $wholesaler->tax_id) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('tax_id') border-red-300 @enderror">
                            @error('tax_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información de Ubicación -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Información de Ubicación</h4>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $wholesaler->address) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('address') border-red-300 @enderror">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                            <input type="text" name="city" id="city"
                                value="{{ old('city', $wholesaler->city) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('city') border-red-300 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state"
                                class="block text-sm font-medium text-gray-700 mb-2">Departamento/Estado</label>
                            <input type="text" name="state" id="state"
                                value="{{ old('state', $wholesaler->state) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('state') border-red-300 @enderror">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">País</label>
                            <input type="text" name="country" id="country"
                                value="{{ old('country', $wholesaler->country) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('country') border-red-300 @enderror">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Código
                                Postal</label>
                            <input type="text" name="postal_code" id="postal_code"
                                value="{{ old('postal_code', $wholesaler->postal_code) }}"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('postal_code') border-red-300 @enderror">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información Comercial -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Información Comercial</h4>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                            <select name="status" id="status" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="enabled"
                                    {{ old('status', $wholesaler->status) == 'enabled' ? 'selected' : '' }}>
                                    Habilitado
                                </option>
                                <option value="disabled"
                                    {{ old('status', $wholesaler->status) == 'disabled' ? 'selected' : '' }}>
                                    No Habilitado</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror">{{ old('notes', $wholesaler->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Información de la Cuenta</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                            <p class="text-sm text-gray-900">{{ $wholesaler->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                @if ($wholesaler->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $wholesaler->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.wholesalers.show', $wholesaler) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Actualizar Mayorista
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
