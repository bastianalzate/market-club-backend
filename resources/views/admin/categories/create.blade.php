@extends('admin.layouts.app')

@section('title', 'Crear Categoría')
@section('page-title', 'Crear Categoría')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header con navegación -->
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
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario principal -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información de la Categoría</h3>
                        <p class="mt-1 text-sm text-gray-500">Completa la información para crear una nueva categoría</p>
                    </div>

                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-6">
                        @csrf

                        <!-- Nombre de la categoría -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la
                                Categoría *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                                placeholder="Ej: Electrónicos, Ropa, Hogar">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea name="description" id="description" rows="4"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                placeholder="Describe brevemente esta categoría...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Imagen -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Imagen de la
                                Categoría</label>

                            <!-- Área de subida de archivo -->
                            <div id="upload-area"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
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

                            <!-- Vista previa de la imagen (oculta inicialmente) -->
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
                                <p class="mt-2 text-sm text-green-600">✓ Archivo subido correctamente</p>
                            </div>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Categoría activa
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Solo las categorías activas son visibles para los
                                clientes</p>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.categories.index') }}"
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
                                Crear Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Vista Previa -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Vista Previa</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <!-- Imagen de vista previa -->
                            <div id="preview-image" class="mb-4 hidden">
                                <img id="preview-img-sidebar" src="" alt="Vista previa"
                                    class="mx-auto h-24 w-24 object-cover rounded-lg border border-gray-200">
                            </div>
                            <div id="no-image" class="mb-4">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Información de la categoría -->
                            <h4 id="preview-name" class="text-lg font-medium text-gray-900 mb-2">Nombre de la categoría
                            </h4>
                            <p id="preview-description" class="text-sm text-gray-500">Descripción de la categoría</p>

                            <!-- Estado -->
                            <div class="mt-4">
                                <span id="preview-status"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Activa
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ayuda</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">El nombre debe ser único y descriptivo</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">Solo las categorías activas son visibles para los
                                        clientes</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">El slug se genera automáticamente del nombre</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">La imagen es opcional pero recomendada</p>
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
            const fileInput = document.getElementById('image');
            const uploadArea = document.getElementById('upload-area');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressPercentage = document.getElementById('progress-percentage');
            const progressText = document.getElementById('progress-text');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const removeImageBtn = document.getElementById('remove-image');
            const previewImage = document.getElementById('preview-image');
            const previewImgSidebar = document.getElementById('preview-img-sidebar');
            const noImage = document.getElementById('no-image');

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

                    // Mostrar barra de progreso
                    progressContainer.classList.remove('hidden');
                    uploadArea.classList.add('hidden');

                    // Simular progreso de subida
                    simulateUpload();

                    // Crear vista previa de la imagen
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImgSidebar.src = e.target.result;
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

            // Manejar eliminación de imagen
            removeImageBtn.addEventListener('click', function() {
                fileInput.value = '';
                imagePreview.classList.add('hidden');
                uploadArea.classList.remove('hidden');
                progressContainer.classList.add('hidden');
                progressBar.style.width = '0%';
                progressPercentage.textContent = '0%';
            });

            // Vista previa en tiempo real
            document.getElementById('name').addEventListener('input', function() {
                document.getElementById('preview-name').textContent = this.value ||
                'Nombre de la categoría';
            });

            document.getElementById('description').addEventListener('input', function() {
                document.getElementById('preview-description').textContent = this.value ||
                    'Descripción de la categoría';
            });

            document.getElementById('is_active').addEventListener('change', function() {
                const statusElement = document.getElementById('preview-status');
                if (this.checked) {
                    statusElement.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                    statusElement.innerHTML =
                        '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Activa';
                } else {
                    statusElement.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                    statusElement.innerHTML =
                        '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>Inactiva';
                }
            });

            // Actualizar vista previa de imagen en sidebar
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImgSidebar.src = e.target.result;
                        previewImage.classList.remove('hidden');
                        noImage.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Validación del formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const nameField = document.getElementById('name');
                if (!nameField.value.trim()) {
                    nameField.classList.add('border-red-300');
                    e.preventDefault();
                    alert('Por favor ingresa el nombre de la categoría.');
                } else {
                    nameField.classList.remove('border-red-300');
                }
            });
        });
    </script>
@endsection
