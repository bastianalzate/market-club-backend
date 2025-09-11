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
                                <img src="{{ $product->image }}" alt="Imagen actual"
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
                                    <input id="image" name="image" type="file" class="sr-only"
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

            // Función para simular progreso de subida
            function simulateUpload() {
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 100) progress = 100;

                    progressBar.style.width = progress + '%';
                    progressPercentage.textContent = Math.round(progress) + '%';

                    if (progress < 30) {
                        progressText.textContent = 'Preparando archivo...';
                    } else if (progress < 60) {
                        progressText.textContent = 'Subiendo archivo...';
                    } else if (progress < 90) {
                        progressText.textContent = 'Procesando imagen...';
                    } else if (progress < 100) {
                        progressText.textContent = 'Finalizando...';
                    } else {
                        progressText.textContent = '¡Archivo subido correctamente!';
                        clearInterval(interval);

                        // Ocultar barra de progreso y mostrar vista previa
                        setTimeout(() => {
                            progressContainer.classList.add('hidden');
                            imagePreview.classList.remove('hidden');
                        }, 500);
                    }
                }, 100);
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

                    // Simular progreso de subida
                    simulateUpload();

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
        });
    </script>
@endsection
