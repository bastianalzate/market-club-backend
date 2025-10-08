@extends('admin.layouts.app')

@section('title', 'Detalles de la Categoría')
@section('page-title', 'Detalles de la Categoría')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header con acciones -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Categorías
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.edit', $category) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Editar Categoría
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información de la Categoría -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información de la Categoría</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Imagen de la categoría -->
                            <div>
                                @if ($category->image)
                                    <div class="relative">
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                            class="w-full h-64 object-cover rounded-lg border border-gray-200">
                                    </div>
                                @else
                                    <div
                                        class="w-full h-64 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                </path>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">Sin imagen</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Detalles de la categoría -->
                            <div class="space-y-4">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                                    @if ($category->description)
                                        <p class="mt-2 text-gray-600">{{ $category->description }}</p>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Slug</label>
                                        <p class="text-sm text-gray-900 font-mono">{{ $category->slug }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Estado</label>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if ($category->is_active) bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    @if ($category->is_active)
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"></path>
                                                    @else
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"></path>
                                                    @endif
                                                </svg>
                                                {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos de la Categoría -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Productos en esta Categoría</h3>
                    </div>
                    <div class="p-6">
                        @if ($category->products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($category->products as $product)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                @if ($product->image_url)
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                        class="h-12 w-12 object-cover rounded-lg border border-gray-200">
                                                @else
                                                    <div
                                                        class="h-12 w-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate">
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="hover:text-indigo-600">
                                                        {{ $product->name }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    ${{ number_format($product->current_price, 2) }}</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if ($product->is_active) bg-green-100 text-green-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                    @if ($product->is_featured)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Destacado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Sin productos</h3>
                                <p class="mt-1 text-sm text-gray-500">Esta categoría no tiene productos asociados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Estadísticas -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Estadísticas</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ $category->products->count() }}</div>
                                <div class="text-sm text-gray-500">Total Productos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $category->products->where('is_active', true)->count() }}</div>
                                <div class="text-sm text-gray-500">Productos Activos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $category->products->where('is_featured', true)->count() }}</div>
                                <div class="text-sm text-gray-500">Productos Destacados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">
                                    ${{ number_format($category->products->sum('current_price'), 2) }}</div>
                                <div class="text-sm text-gray-500">Valor Total</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Técnica -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información Técnica</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $category->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $category->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Creado</dt>
                                <dd class="text-sm text-gray-900">{{ $category->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Actualizado</dt>
                                <dd class="text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Editar Categoría
                        </a>

                        @if ($category->is_active)
                            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                                    onclick="return confirm('¿Desactivar esta categoría?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21">
                                        </path>
                                    </svg>
                                    Desactivar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Activar
                                </button>
                            </form>
                        @endif

                        @if ($category->products->count() == 0)
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                    onclick="return confirm('¿Estás seguro de eliminar esta categoría? Esta acción no se puede deshacer.')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @else
                            <div class="text-center py-2">
                                <p class="text-xs text-gray-500">No se puede eliminar una categoría con productos</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
