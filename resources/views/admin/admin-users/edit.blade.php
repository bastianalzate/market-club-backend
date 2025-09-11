@extends('admin.layouts.app')

@section('title', 'Editar Administrador')
@section('page-title', 'Editar Administrador')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.admin-users.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Administradores
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.admin-users.show', $adminUser) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Administrador
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario principal -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Editar Administrador</h3>
                        <p class="mt-1 text-sm text-gray-500">Modifica la información del administrador</p>
                    </div>

                    <form action="{{ route('admin.admin-users.update', $adminUser) }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Información básica -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo
                                    *</label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $adminUser->name) }}" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                                    placeholder="Ej: Juan Pérez">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo
                                    Electrónico *</label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $adminUser->email) }}" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                                    placeholder="admin@marketclub.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                                <select name="role" id="role" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('role') border-red-300 @enderror">
                                    <option value="">Selecciona un rol</option>
                                    <option value="admin" {{ old('role', $adminUser->role) == 'admin' ? 'selected' : '' }}>
                                        Administrador</option>
                                    <option value="super_admin"
                                        {{ old('role', $adminUser->role) == 'super_admin' ? 'selected' : '' }}>Super
                                        Administrador</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva
                                    Contraseña</label>
                                <input type="password" name="password" id="password"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 @enderror"
                                    placeholder="Dejar vacío para mantener la actual">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Dejar vacío si no quieres cambiar la contraseña</p>
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Repite la nueva contraseña">
                            </div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                    {{ old('is_active', $adminUser->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Administrador activo
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Solo los administradores activos pueden iniciar sesión</p>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.admin-users.show', $adminUser) }}"
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
                                Actualizar Administrador
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Información Actual -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información Actual</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nombre</label>
                                <p class="text-sm text-gray-900">{{ $adminUser->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-sm text-gray-900">{{ $adminUser->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Rol</label>
                                <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $adminUser->role)) }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Estado</label>
                                <p class="text-sm text-gray-900">{{ $adminUser->is_active ? 'Activo' : 'Inactivo' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Creado</label>
                                <p class="text-sm text-gray-900">{{ $adminUser->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Roles -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tipos de Administrador</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-400 pl-4">
                                <h4 class="text-sm font-medium text-gray-900">Administrador</h4>
                                <p class="text-sm text-gray-600 mt-1">Puede gestionar productos, categorías, órdenes y
                                    clientes. Acceso completo al panel de administración.</p>
                            </div>

                            <div class="border-l-4 border-purple-400 pl-4">
                                <h4 class="text-sm font-medium text-gray-900">Super Administrador</h4>
                                <p class="text-sm text-gray-600 mt-1">Acceso completo incluyendo gestión de otros
                                    administradores. Permisos máximos del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertencias -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Advertencias</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">No se puede eliminar el último super administrador</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">Los cambios se aplicarán inmediatamente</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">La contraseña es opcional al editar</p>
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
                const requiredFields = ['name', 'email', 'role'];
                let isValid = true;

                requiredFields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (!field.value.trim()) {
                        field.classList.add('border-red-300');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-300');
                    }
                });

                // Validar que las contraseñas coincidan si se proporcionan
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;

                if (password && password !== passwordConfirmation) {
                    document.getElementById('password_confirmation').classList.add('border-red-300');
                    isValid = false;
                } else {
                    document.getElementById('password_confirmation').classList.remove('border-red-300');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(
                        'Por favor completa todos los campos obligatorios y asegúrate de que las contraseñas coincidan.');
                }
            });

            // Validación en tiempo real de contraseñas
            const passwordField = document.getElementById('password');
            const passwordConfirmationField = document.getElementById('password_confirmation');

            function validatePasswords() {
                if (passwordField.value && passwordConfirmationField.value) {
                    if (passwordField.value !== passwordConfirmationField.value) {
                        passwordConfirmationField.classList.add('border-red-300');
                    } else {
                        passwordConfirmationField.classList.remove('border-red-300');
                    }
                }
            }

            passwordField.addEventListener('input', validatePasswords);
            passwordConfirmationField.addEventListener('input', validatePasswords);
        });
    </script>
@endsection
