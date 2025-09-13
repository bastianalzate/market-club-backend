@extends('admin.layouts.app')

@section('title', 'Editar Producto')
@section('page-title', 'Editar Producto')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Productos
                </a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Editar Producto</h3>
                <p class="mt-1 text-sm text-gray-500">Modifica la información del producto</p>
            </div>

            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Información Básica -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Producto
                            *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sku') border-red-300 @enderror">
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                        <select name="category_id" id="category_id" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('category_id') border-red-300 @enderror">
                            <option value="">Selecciona una categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                            Producto</label>
                        <select name="product_type_id" id="product_type_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('product_type_id') border-red-300 @enderror">
                            <option value="">Selecciona un tipo de producto</option>
                            @foreach ($productTypes as $productType)
                                <option value="{{ $productType->id }}"
                                    {{ old('product_type_id', $product->product_type_id) == $productType->id ? 'selected' : '' }}>
                                    {{ $productType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Campos Específicos del Tipo de Producto -->
                <div id="product-type-fields" class="{{ $product->product_type_id ? '' : 'hidden' }}">
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Características Específicas</h3>


                        <!-- Campos estáticos para cervezas -->
                        @if ($product->product_type_id && $product->productType && $product->productType->slug === 'cervezas')
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- País de Origen -->
                                <div>
                                    <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-2">País
                                        de Origen *</label>
                                    <select name="country_of_origin" id="country_of_origin" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecciona un país</option>
                                        <option value="Inglaterra"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Inglaterra' ? 'selected' : '' }}>
                                            Inglaterra</option>
                                        <option value="Colombia"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Colombia' ? 'selected' : '' }}>
                                            Colombia</option>
                                        <option value="Alemania"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Alemania' ? 'selected' : '' }}>
                                            Alemania</option>
                                        <option value="Italia"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Italia' ? 'selected' : '' }}>
                                            Italia</option>
                                        <option value="Escocia"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Escocia' ? 'selected' : '' }}>
                                            Escocia</option>
                                        <option value="Bélgica"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Bélgica' ? 'selected' : '' }}>
                                            Bélgica</option>
                                        <option value="España"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'España' ? 'selected' : '' }}>
                                            España</option>
                                        <option value="Países Bajos"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Países Bajos' ? 'selected' : '' }}>
                                            Países Bajos</option>
                                        <option value="Japón"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Japón' ? 'selected' : '' }}>
                                            Japón</option>
                                        <option value="México"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'México' ? 'selected' : '' }}>
                                            México</option>
                                        <option value="Perú"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Perú' ? 'selected' : '' }}>
                                            Perú</option>
                                        <option value="República Checa"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'República Checa' ? 'selected' : '' }}>
                                            República Checa</option>
                                        <option value="Estados Unidos"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Estados Unidos' ? 'selected' : '' }}>
                                            Estados Unidos</option>
                                        <option value="Tailandia"
                                            {{ ($product->product_specific_data['country_of_origin'] ?? '') === 'Tailandia' ? 'selected' : '' }}>
                                            Tailandia</option>
                                    </select>
                                </div>

                                <!-- Tamaño -->
                                <div>
                                    <label for="volume_ml" class="block text-sm font-medium text-gray-700 mb-2">Tamaño
                                        (ml)</label>
                                    <select name="volume_ml" id="volume_ml"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecciona un tamaño</option>
                                        <option value="250"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '250' ? 'selected' : '' }}>
                                            250 ml</option>
                                        <option value="269"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '269' ? 'selected' : '' }}>
                                            269 ml</option>
                                        <option value="300"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '300' ? 'selected' : '' }}>
                                            300 ml</option>
                                        <option value="310"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '310' ? 'selected' : '' }}>
                                            310 ml</option>
                                        <option value="330"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '330' ? 'selected' : '' }}>
                                            330 ml</option>
                                        <option value="355"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '355' ? 'selected' : '' }}>
                                            355 ml</option>
                                        <option value="500"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '500' ? 'selected' : '' }}>
                                            500 ml</option>
                                        <option value="5000"
                                            {{ ($product->product_specific_data['volume_ml'] ?? '') === '5000' ? 'selected' : '' }}>
                                            5000 ml</option>
                                    </select>
                                </div>

                                <!-- Tipo de Envase -->
                                <div>
                                    <label for="packaging_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                                        Envase</label>
                                    <select name="packaging_type" id="packaging_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecciona un tipo</option>
                                        <option value="botella"
                                            {{ ($product->product_specific_data['packaging_type'] ?? '') === 'botella' ? 'selected' : '' }}>
                                            Botella</option>
                                        <option value="lata"
                                            {{ ($product->product_specific_data['packaging_type'] ?? '') === 'lata' ? 'selected' : '' }}>
                                            Lata</option>
                                        <option value="barril"
                                            {{ ($product->product_specific_data['packaging_type'] ?? '') === 'barril' ? 'selected' : '' }}>
                                            Barril</option>
                                        <option value="growler"
                                            {{ ($product->product_specific_data['packaging_type'] ?? '') === 'growler' ? 'selected' : '' }}>
                                            Growler</option>
                                    </select>
                                </div>

                                <!-- Contenido de Alcohol -->
                                <div>
                                    <label for="alcohol_content"
                                        class="block text-sm font-medium text-gray-700 mb-2">Contenido de Alcohol
                                        (%)</label>
                                    <input type="number" name="alcohol_content" id="alcohol_content" step="0.1"
                                        min="0" max="100"
                                        value="{{ $product->product_specific_data['alcohol_content'] ?? '' }}"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <!-- Estilo de Cerveza -->
                                <div>
                                    <label for="beer_style" class="block text-sm font-medium text-gray-700 mb-2">Estilo de
                                        Cerveza</label>
                                    <select name="beer_style" id="beer_style"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecciona un estilo</option>
                                        <option value="lager"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'lager' ? 'selected' : '' }}>
                                            Lager</option>
                                        <option value="pilsner"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'pilsner' ? 'selected' : '' }}>
                                            Pilsner</option>
                                        <option value="ale"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'ale' ? 'selected' : '' }}>
                                            Ale</option>
                                        <option value="ipa"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'ipa' ? 'selected' : '' }}>
                                            IPA</option>
                                        <option value="stout"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'stout' ? 'selected' : '' }}>
                                            Stout</option>
                                        <option value="porter"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'porter' ? 'selected' : '' }}>
                                            Porter</option>
                                        <option value="wheat"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'wheat' ? 'selected' : '' }}>
                                            Wheat Beer</option>
                                        <option value="pale_ale"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'pale_ale' ? 'selected' : '' }}>
                                            Pale Ale</option>
                                        <option value="amber"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'amber' ? 'selected' : '' }}>
                                            Amber</option>
                                        <option value="brown"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'brown' ? 'selected' : '' }}>
                                            Brown Ale</option>
                                        <option value="blonde"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'blonde' ? 'selected' : '' }}>
                                            Blonde</option>
                                        <option value="dark"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'dark' ? 'selected' : '' }}>
                                            Dark Beer</option>
                                        <option value="light"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'light' ? 'selected' : '' }}>
                                            Light Beer</option>
                                        <option value="craft"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'craft' ? 'selected' : '' }}>
                                            Craft Beer</option>
                                        <option value="imported"
                                            {{ ($product->product_specific_data['beer_style'] ?? '') === 'imported' ? 'selected' : '' }}>
                                            Imported</option>
                                    </select>
                                </div>

                                <!-- Cervecería -->
                                <div>
                                    <label for="brewery"
                                        class="block text-sm font-medium text-gray-700 mb-2">Cervecería</label>
                                    <input type="text" name="brewery" id="brewery" maxlength="255"
                                        value="{{ $product->product_specific_data['brewery'] ?? '' }}"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <!-- Ingredientes -->
                                <div class="sm:col-span-2">
                                    <label for="ingredients"
                                        class="block text-sm font-medium text-gray-700 mb-2">Ingredientes</label>
                                    <textarea name="ingredients" id="ingredients" rows="3"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $product->product_specific_data['ingredients'] ?? '' }}</textarea>
                                </div>

                                <!-- Notas de Cata -->
                                <div class="sm:col-span-2">
                                    <label for="tasting_notes" class="block text-sm font-medium text-gray-700 mb-2">Notas
                                        de Cata</label>
                                    <textarea name="tasting_notes" id="tasting_notes" rows="4"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $product->product_specific_data['tasting_notes'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Precios y Stock -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Precio *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" min="0"
                                value="{{ old('price', $product->price) }}" required
                                class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('price') border-red-300 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Precio de
                            Oferta</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="sale_price" id="sale_price" step="0.01" min="0"
                                value="{{ old('sale_price', $product->sale_price) }}"
                                class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sale_price') border-red-300 @enderror">
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="0"
                            value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('stock_quantity') border-red-300 @enderror">
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Imagen -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Imagen Principal</label>

                    <!-- Imagen actual (si existe) -->
                    @if ($product->image)
                        <div id="current-image" class="mb-4">
                            <div class="relative inline-block">
                                <img src="{{ Storage::url($product->image) }}" alt="Imagen actual"
                                    class="h-32 w-32 object-cover rounded-lg border border-gray-300">
                                <button type="button" id="remove-current-image"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Imagen actual</p>
                        </div>
                    @endif

                    <!-- Área de subida de archivo -->
                    <div id="upload-area"
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors {{ $product->image ? 'hidden' : '' }}">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Subir archivo</span>
                                    <input id="image" name="image_file" type="file" class="sr-only"
                                        accept="image/*">
                                </label>
                                <p class="pl-1">o arrastra y suelta</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                        </div>
                    </div>

                    <!-- Barra de progreso (oculta inicialmente) -->
                    <div id="progress-container" class="mt-4 hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Subiendo archivo...</span>
                            <span id="progress-percentage" class="text-sm text-gray-500">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progress-bar"
                                class="bg-indigo-600 h-2 rounded-full transition-all duration-300 ease-out"
                                style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="mt-2 text-sm text-gray-600">Preparando archivo...</p>
                    </div>

                    <!-- Vista previa de la nueva imagen (oculta inicialmente) -->
                    <div id="image-preview" class="mt-4 hidden">
                        <div class="relative inline-block">
                            <img id="preview-img" src="" alt="Vista previa"
                                class="h-32 w-32 object-cover rounded-lg border border-gray-300">
                            <button type="button" id="remove-image"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-green-600">✓ Nueva imagen subida correctamente</p>
                    </div>

                    <!-- Campo oculto para la URL de la imagen -->
                    <input type="hidden" name="image" id="image-url" value="{{ old('image', $product->image) }}">

                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opciones -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Producto activo
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input id="is_featured" name="is_featured" type="checkbox" value="1"
                                {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                Producto destacado
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="attributes" class="block text-sm font-medium text-gray-700 mb-2">Atributos
                            (JSON)</label>
                        <textarea name="attributes" id="attributes" rows="3" placeholder='{"color": "rojo", "talla": "M"}'
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('attributes') border-red-300 @enderror">{{ old('attributes', $product->attributes) }}</textarea>
                        @error('attributes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('image');
            const uploadArea = document.getElementById('upload-area');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressPercentage = document.getElementById('progress-percentage');
            const progressText = document.getElementById('progress-text');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const removeImageBtn = document.getElementById('remove-image');
            const currentImage = document.getElementById('current-image');
            const removeCurrentImageBtn = document.getElementById('remove-current-image');

            // Función para subir imagen real
            async function uploadImage(file) {
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const response = await fetch('{{ route('admin.images.upload') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Guardar la ruta relativa de la imagen
                        document.getElementById('image-url').value = result.path;

                        // Ocultar imagen actual si existe
                        if (currentImage) {
                            currentImage.classList.add('hidden');
                        }

                        // Mostrar mensaje de éxito
                        progressText.textContent = '¡Archivo subido correctamente!';

                        // Ocultar barra de progreso y mostrar vista previa
                        setTimeout(() => {
                            progressContainer.classList.add('hidden');
                            imagePreview.classList.remove('hidden');
                        }, 500);
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error al subir imagen:', error);
                    alert('Error al subir la imagen: ' + error.message);

                    // Resetear el formulario
                    progressContainer.classList.add('hidden');
                    uploadArea.classList.remove('hidden');
                    fileInput.value = '';
                }
            }

            // Manejar selección de archivo
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar tipo de archivo
                    if (!file.type.startsWith('image/')) {
                        alert('Por favor selecciona un archivo de imagen válido.');
                        return;
                    }

                    // Validar tamaño (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. Máximo 10MB.');
                        return;
                    }

                    // Ocultar imagen actual si existe
                    if (currentImage) {
                        currentImage.classList.add('hidden');
                    }

                    // Mostrar barra de progreso
                    progressContainer.classList.remove('hidden');
                    uploadArea.classList.add('hidden');

                    // Subir la imagen
                    uploadImage(file);

                    // Crear vista previa de la imagen
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Manejar drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });

            // Manejar eliminación de nueva imagen
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                    fileInput.value = '';
                    imagePreview.classList.add('hidden');
                    uploadArea.classList.remove('hidden');
                    progressContainer.classList.add('hidden');
                    progressBar.style.width = '0%';
                    progressPercentage.textContent = '0%';

                    // Mostrar imagen actual si existe
                    if (currentImage) {
                        currentImage.classList.remove('hidden');
                    }
                });
            }

            // Manejar eliminación de imagen actual
            if (removeCurrentImageBtn) {
                removeCurrentImageBtn.addEventListener('click', function() {
                    currentImage.classList.add('hidden');
                    uploadArea.classList.remove('hidden');
                });
            }

            // Validación del formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const requiredFields = ['name', 'sku', 'category_id', 'price', 'stock_quantity'];
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

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos obligatorios.');
                }
            });

            // Manejar cambio de tipo de producto
            const productTypeSelect = document.getElementById('product_type_id');
            const productTypeFields = document.getElementById('product-type-fields');
            const dynamicFields = document.getElementById('dynamic-fields');

            const productTypeConfigs = {
                @foreach ($productTypes as $productType)
                    {{ $productType->id }}: @json($productType->getFieldsConfig()),
                @endforeach
            };

            // Función para crear elementos de campo
            function createFieldElement(fieldName, fieldConfig) {
                const div = document.createElement('div');
                div.className = 'sm:col-span-2';

                const label = document.createElement('label');
                label.className = 'block text-sm font-medium text-gray-700 mb-2';
                label.textContent = fieldConfig.label + (fieldConfig.required ? ' *' : '');
                label.setAttribute('for', fieldName);

                let input;
                const productData = @json($product->product_specific_data ?? []);
                const currentValue = productData[fieldName] || '';

                console.log(`Campo ${fieldName}: valor actual = "${currentValue}"`);

                if (fieldConfig.type === 'select') {
                    input = document.createElement('select');
                    input.className =
                        'block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
                    if (fieldConfig.required) input.required = true;

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Selecciona una opción';
                    input.appendChild(defaultOption);

                    Object.entries(fieldConfig.options).forEach(([value, label]) => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = label;
                        if (currentValue === value) {
                            option.selected = true;
                            console.log(`Seleccionando opción: ${value} para campo ${fieldName}`);
                        }
                        input.appendChild(option);
                    });
                } else if (fieldConfig.type === 'textarea') {
                    input = document.createElement('textarea');
                    input.className =
                        'block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
                    input.rows = fieldConfig.rows || 3;
                    input.value = currentValue;
                    if (fieldConfig.required) input.required = true;
                } else if (fieldConfig.type === 'number') {
                    input = document.createElement('input');
                    input.type = 'number';
                    input.className =
                        'block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
                    input.value = currentValue;
                    if (fieldConfig.min !== undefined) input.min = fieldConfig.min;
                    if (fieldConfig.max !== undefined) input.max = fieldConfig.max;
                    if (fieldConfig.step !== undefined) input.step = fieldConfig.step;
                    if (fieldConfig.required) input.required = true;
                } else {
                    input = document.createElement('input');
                    input.type = fieldConfig.type || 'text';
                    input.className =
                        'block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
                    input.value = currentValue;
                    if (fieldConfig.maxlength) input.maxLength = fieldConfig.maxlength;
                    if (fieldConfig.required) input.required = true;
                }

                input.name = fieldName;
                input.id = fieldName;

                div.appendChild(label);
                div.appendChild(input);

                return div;
            }

            // Función para cargar campos del tipo de producto actual
            function loadCurrentProductTypeFields() {
                const selectedTypeId = productTypeSelect.value;
                dynamicFields.innerHTML = '';

                // Debug: mostrar datos del producto
                const productData = @json($product->product_specific_data ?? []);
                console.log('Datos del producto:', productData);
                console.log('Tipo de producto seleccionado:', selectedTypeId);

                if (selectedTypeId && productTypeConfigs[selectedTypeId]) {
                    const fieldsConfig = productTypeConfigs[selectedTypeId];
                    Object.entries(fieldsConfig).forEach(([fieldName, fieldConfig]) => {
                        const fieldElement = createFieldElement(fieldName, fieldConfig);
                        dynamicFields.appendChild(fieldElement);
                    });
                    productTypeFields.classList.remove('hidden');
                } else {
                    productTypeFields.classList.add('hidden');
                }
            }

            // Manejar cambio de tipo de producto - deshabilitado para evitar duplicación
            // productTypeSelect.addEventListener('change', loadCurrentProductTypeFields);

            // Cargar campos iniciales - deshabilitado para evitar duplicación
            // setTimeout(() => {
            //     loadCurrentProductTypeFields();
            // }, 100);

            // También cargar cuando la página esté completamente cargada - deshabilitado
            // window.addEventListener('load', function() {
            //     setTimeout(() => {
            //         loadCurrentProductTypeFields();
            //     }, 200);
            // });
        });
    </script>
@endsection
