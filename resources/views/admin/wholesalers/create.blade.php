@extends('admin.layouts.app')

@section('title', 'Crear Mayorista')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Crear Nuevo Mayorista</h1>
                        <p class="mt-2 text-sm text-gray-700">Completa la información del mayorista</p>
                    </div>

                    <form action="{{ route('admin.wholesalers.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Información del Negocio -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Negocio</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="business_name" class="block text-sm font-medium text-gray-700">Nombre del
                                        Negocio *</label>
                                    <input type="text" name="business_name" id="business_name"
                                        value="{{ old('business_name') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('business_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="business_type" class="block text-sm font-medium text-gray-700">Tipo de
                                        Negocio *</label>
                                    <select name="business_type" id="business_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Seleccionar tipo</option>
                                        <option value="restaurant"
                                            {{ old('business_type') == 'restaurant' ? 'selected' : '' }}>Restaurante
                                        </option>
                                        <option value="bar" {{ old('business_type') == 'bar' ? 'selected' : '' }}>Bar
                                        </option>
                                        <option value="retail_store"
                                            {{ old('business_type') == 'retail_store' ? 'selected' : '' }}>Tienda</option>
                                        <option value="distributor"
                                            {{ old('business_type') == 'distributor' ? 'selected' : '' }}>Distribuidor
                                        </option>
                                        <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Otro
                                        </option>
                                    </select>
                                    @error('business_type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="business_description"
                                        class="block text-sm font-medium text-gray-700">Descripción del Negocio</label>
                                    <textarea name="business_description" id="business_description" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('business_description') }}</textarea>
                                    @error('business_description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="contact_name" class="block text-sm font-medium text-gray-700">Nombre de
                                        Contacto *</label>
                                    <input type="text" name="contact_name" id="contact_name"
                                        value="{{ old('contact_name') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('contact_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700">NIT/RUT</label>
                                    <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('tax_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Ubicación -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Ubicación</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('city')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="state"
                                        class="block text-sm font-medium text-gray-700">Departamento/Estado</label>
                                    <input type="text" name="state" id="state" value="{{ old('state') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('state')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">País</label>
                                    <input type="text" name="country" id="country"
                                        value="{{ old('country', 'Colombia') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('country')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Código
                                        Postal</label>
                                    <input type="text" name="postal_code" id="postal_code"
                                        value="{{ old('postal_code') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('postal_code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Comercial -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Comercial</h3>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                                <div class="sm:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.wholesalers.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Crear Mayorista
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
