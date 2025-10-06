<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Market Club</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        'primary-dark': '#3730A3',
                        secondary: '#64748B',
                        success: '#059669',
                        warning: '#D97706',
                        danger: '#DC2626',
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>

<body class="bg-gray-50">
    <div class="flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="px-4 mx-auto">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center -m-2 xl:hidden">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 text-gray-400 bg-white rounded-lg hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                            onclick="toggleSidebar()">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex ml-6 xl:ml-0">
                        <div class="flex items-center flex-shrink-0">
                            <i class="fas fa-store text-2xl text-primary mr-2"></i>
                            <span class="text-xl font-bold text-gray-900">Market Club</span>
                        </div>
                    </div>

                    <div class="flex-1 hidden max-w-xs ml-40 mr-auto lg:block">
                        <label for="search" class="sr-only">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="search" name="search" id="search"
                                class="block w-full py-2 pl-10 border border-gray-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm"
                                placeholder="Buscar..." />
                        </div>
                    </div>

                    <div class="flex items-center justify-end ml-auto space-x-6">
                        <div class="relative">
                            <button type="button" onclick="openContactsModal()"
                                class="p-1 text-gray-700 transition-all duration-200 bg-white rounded-full hover:text-gray-900 focus:outline-none hover:bg-gray-100">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </button>
                            @php
                                $newContactsCount = \App\Models\Contact::new()->count();
                            @endphp
                            @if($newContactsCount > 0)
                                <span id="contactsBadge"
                                    class="inline-flex items-center justify-center px-1 absolute -top-px -right-1 py-0.5 rounded-full text-[10px] font-medium bg-indigo-600 text-white min-w-[14px] h-3.5">
                                    {{ $newContactsCount > 9 ? '9+' : $newContactsCount }}
                                </span>
                            @endif
                        </div>

                        {{-- Botón de notificaciones (campana) oculto temporalmente --}}
                        {{-- <div class="relative">
                            <button type="button"
                                class="p-1 text-gray-700 transition-all duration-200 bg-white rounded-full hover:text-gray-900 focus:outline-none hover:bg-gray-100">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                            </button>
                        </div> --}}

                        <div class="relative">
                            <button type="button"
                                class="flex items-center max-w-xs rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                                <div class="w-9 h-9 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <div class="hidden xl:flex xl:w-64 xl:flex-col" id="sidebar">
                <div class="flex flex-col pt-5 overflow-y-auto">
                    <div class="flex flex-col justify-between flex-1 h-full px-4">
                        <div class="space-y-4">
                            <div>
                                <a href="{{ route('admin.products.create') }}"
                                    class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold leading-5 text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:bg-indigo-500">
                                    <svg class="w-5 h-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Nuevo Producto
                                </a>
                            </div>

                            <nav class="flex-1 space-y-1">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </a>
                            </nav>

                            <div>
                                <p class="px-4 text-xs font-semibold tracking-widest text-gray-400 uppercase">Tienda
                                </p>
                                <nav class="flex-1 mt-4 space-y-1">
                                    <a href="{{ route('admin.products.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Productos
                                    </a>

                                    <a href="{{ route('admin.categories.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Categorías
                                    </a>

                                    <a href="{{ route('admin.orders.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Órdenes
                                    </a>

                                    <a href="{{ route('admin.payment-transactions.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.payment-transactions.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Pagos
                                    </a>
                                </nav>
                            </div>

                            <div>
                                <p class="px-4 text-xs font-semibold tracking-widest text-gray-400 uppercase">Usuarios
                                </p>
                                <nav class="flex-1 mt-4 space-y-1">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Clientes
                                    </a>

                                    <a href="{{ route('admin.wholesalers.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.wholesalers.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Mayoristas
                                    </a>

                                    <a href="{{ route('admin.contacts.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.contacts.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Contactos
                                    </a>

                                    <a href="{{ route('admin.admin-users.index') }}"
                                        class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.admin-users.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-900' }} rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Administradores
                                    </a>
                                </nav>
                            </div>
                        </div>

                        <div class="pb-4 mt-12">
                            <nav class="flex-1 space-y-1">
                                <a href="{{ route('home') }}"
                                    class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 text-gray-900 rounded-lg hover:bg-gray-200 group">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ver Tienda
                                </a>

                                <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-2.5 text-sm font-medium transition-all duration-200 text-gray-900 rounded-lg hover:bg-gray-200 group">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-x-hidden">
                <main>
                    <div class="py-6">
                        <div class="px-4 mx-auto sm:px-6 md:px-8">
                            @if (session('success'))
                                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }

        // Función para actualizar el badge del contador (solo para el modal)
        function updateContactsBadge(count) {
            const badge = document.getElementById('contactsBadge');
            if (!badge) {
                console.error('❌ Badge element not found');
                return;
            }
            
            if (count > 9) {
                badge.textContent = '9+';
            } else {
                badge.textContent = count.toString();
            }
            
            // Ocultar el badge si no hay contactos nuevos
            if (count === 0) {
                badge.style.display = 'none';
            } else {
                badge.style.display = 'inline-flex';
            }
            
            console.log('✅ Badge updated with count:', count);
        }
    </script>

    <!-- Modal de Contactos Nuevos -->
    <div id="contactsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header del Modal -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Mensajes Nuevos</h3>
                    <button onclick="closeContactsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div id="contactsContent" class="max-h-96 overflow-y-auto">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                        <p class="mt-2 text-gray-600">Cargando mensajes...</p>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="flex justify-end mt-4">
                    <button onclick="closeContactsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cerrar
                    </button>
                    <a href="{{ route('admin.contacts.index', ['status' => 'new']) }}" 
                        class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Ver Todos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openContactsModal() {
            document.getElementById('contactsModal').classList.remove('hidden');
            loadNewContacts();
        }

        function closeContactsModal() {
            document.getElementById('contactsModal').classList.add('hidden');
        }

        function loadNewContacts() {
            fetch('{{ route("admin.contacts.index") }}?status=new&format=json', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const content = document.getElementById('contactsContent');
                    if (data.contacts && data.contacts.length > 0) {
                        content.innerHTML = data.contacts.map(contact => `
                            <div class="border-b border-gray-200 py-3">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-indigo-600 text-sm font-medium">
                                                ${contact.first_name.charAt(0)}${contact.last_name.charAt(0)}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                ${contact.first_name} ${contact.last_name}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                ${new Date(contact.created_at).toLocaleDateString('es-ES')}
                                            </p>
                                        </div>
                                        <p class="text-xs text-gray-500">${contact.email}</p>
                                        <p class="text-sm text-gray-600 mt-1 truncate">
                                            ${contact.message.length > 100 ? contact.message.substring(0, 100) + '...' : contact.message}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        content.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">No hay mensajes nuevos</p></div>';
                    }
                    
                    // Actualizar el contador del badge
                    updateContactsBadge(data.contacts ? data.contacts.length : 0);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('contactsContent').innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error al cargar los mensajes</p></div>';
                });
        }

        // Función para actualizar el badge
        function updateContactsBadge(count) {
            const badge = document.getElementById('contactsBadge');
            if (count > 9) {
                badge.textContent = '9+';
            } else {
                badge.textContent = count.toString();
            }
            
            // Ocultar el badge si no hay contactos nuevos
            if (count === 0) {
                badge.style.display = 'none';
            } else {
                badge.style.display = 'inline-flex';
            }
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('contactsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContactsModal();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
