<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-pj {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 font-pj">
    <section class="py-12 bg-gray-50 sm:py-16 lg:py-20">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="relative max-w-md mx-auto lg:max-w-lg">
                <div class="absolute -inset-2">
                    <div class="w-full h-full mx-auto rounded-3xl opacity-30 blur-lg filter"
                        style="background: linear-gradient(90deg, #44ff9a -0.55%, #44b0ff 22.86%, #8b44ff 48.36%, #ff6644 73.33%, #ebff70 99.34%)">
                    </div>
                </div>

                <div class="relative overflow-hidden bg-white shadow-xl rounded-xl">
                    <div class="px-4 py-6 sm:px-8">
                        <div class="flex items-center justify-between">
                            <h1 class="text-xl font-bold text-gray-900 font-pj">Iniciar Sesión</h1>
                            <p class="text-base font-normal text-gray-900 font-pj">
                                Panel de <span class="font-bold text-indigo-600">Administración</span>
                            </p>
                        </div>

                        <!-- Mostrar mensajes de éxito/error -->
                        @if (session('success'))
                            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.login') }}" method="POST" class="mt-12">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="email"
                                        class="text-base font-medium text-gray-900 font-pj">Email</label>
                                    <div class="mt-2.5">
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                            placeholder="Email address"
                                            class="block w-full px-4 py-4 text-gray-900 placeholder-gray-600 bg-white border border-gray-400 rounded-xl focus:border-gray-900 focus:ring-gray-900 caret-gray-900 @error('email') border-red-500 @enderror"
                                            required />
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="password"
                                            class="text-base font-medium text-gray-900 font-pj">Contraseña</label>
                                        <a href="#" title=""
                                            class="text-base font-medium text-gray-500 rounded font-pj hover:text-gray-900 hover:underline focus:outline-none focus:ring-1 focus:ring-gray-900 focus:ring-offset-2">¿Olvidaste
                                            tu contraseña?</a>
                                    </div>
                                    <div class="mt-2.5">
                                        <input type="password" name="password" id="password"
                                            placeholder="Contraseña (mín. 8 caracteres)"
                                            class="block w-full px-4 py-4 text-gray-900 placeholder-gray-600 bg-white border border-gray-400 rounded-xl focus:border-gray-900 focus:ring-gray-900 caret-gray-900 @error('password') border-red-500 @enderror"
                                            required />
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="relative flex items-center mt-4">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="remember" id="remember"
                                            class="w-5 h-5 text-gray-900 border-gray-300 rounded focus:ring-gray-900" />
                                    </div>
                                    <div class="ml-3 text-base">
                                        <label for="remember"
                                            class="font-normal text-gray-900 font-pj">Recordarme</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="flex items-center justify-center w-full px-8 py-4 mt-5 text-base font-bold text-white transition-all duration-200 bg-gray-900 border border-transparent rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 font-pj hover:bg-gray-600">
                                Iniciar Sesión
                            </button>
                        </form>

                        <svg class="w-auto h-4 mx-auto mt-8 text-gray-300" viewBox="0 0 172 16" fill="none"
                            stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 11 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 46 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 81 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 116 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 151 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 18 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 53 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 88 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 123 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 158 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 25 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 60 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 95 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 130 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 165 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 32 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 67 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 102 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 137 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 172 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 39 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 74 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 109 1)" />
                            <line y1="-0.5" x2="18.0278" y2="-0.5"
                                transform="matrix(-0.5547 0.83205 0.83205 0.5547 144 1)" />
                        </svg>

                        <a href="{{ route('home') }}" title=""
                            class="
                                flex
                                items-center
                                justify-center
                                w-full
                                px-8
                                py-4
                                mt-8
                                text-base
                                font-bold
                                text-gray-900
                                transition-all
                                duration-200
                                bg-gray-100
                                border border-transparent
                                rounded-xl
                                hover:bg-gray-200
                                focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200
                                font-pj
                            "
                            role="button">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver a la Tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
