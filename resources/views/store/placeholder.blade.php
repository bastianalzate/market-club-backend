<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Club - Próximamente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-inter {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen font-inter">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Card Principal -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header con gradiente -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4">Market Club</h1>
                    <div class="inline-flex items-center px-4 py-2 bg-orange-500 rounded-full">
                        <svg class="w-4 h-4 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white font-medium">Próximamente</span>
                    </div>
                </div>

                <!-- Contenido Principal -->
                <div class="px-8 py-12">
                    <!-- Texto de introducción -->
                    <div class="text-center mb-12">
                        <p class="text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto">
                            Estamos trabajando en crear la mejor experiencia de compras online.
                            Nuestra tienda estará disponible muy pronto con productos increíbles
                            y precios competitivos.
                        </p>
                    </div>

                    <!-- Características -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <!-- Compra Fácil -->
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-xl mb-4 group-hover:bg-blue-200 transition-colors duration-200">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Compra Fácil</h3>
                            <p class="text-gray-600">Proceso de compra simple y seguro</p>
                        </div>

                        <!-- Envío Rápido -->
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-xl mb-4 group-hover:bg-green-200 transition-colors duration-200">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Envío Rápido</h3>
                            <p class="text-gray-600">Entrega rápida a todo el país</p>
                        </div>

                        <!-- Pagos Seguros -->
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-xl mb-4 group-hover:bg-purple-200 transition-colors duration-200">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pagos Seguros</h3>
                            <p class="text-gray-600">Transacciones protegidas y seguras</p>
                        </div>
                    </div>

                    <!-- Separador decorativo -->
                    <div class="flex items-center justify-center mb-8">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <div class="px-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <!-- Botón de acceso al admin -->
                    <div class="text-center">
                        <a href="{{ route('admin.login') }}"
                            class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Acceder al Panel de Administración
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-6 text-center">
                    <p class="text-sm text-gray-500">
                        © {{ date('Y') }} Market Club. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
