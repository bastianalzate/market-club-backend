<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WholesalerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('store.placeholder');
})->name('home');

// Ruta para servir imágenes desde public/uploads/
// Similar al patrón usado en GuinnessBC
Route::get('/uploads/{path}', [App\Http\Controllers\Admin\ImageController::class, 'serve'])
    ->where('path', '.*')
    ->name('uploads.serve');

// Rutas de autenticación del admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rutas del panel de administración (protegidas)
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-sales', [DashboardController::class, 'exportSales'])->name('dashboard.export-sales');
    
    // Gestión de productos
    Route::resource('products', ProductController::class);
    
    // Gestión de imágenes
    Route::post('/images/upload', [App\Http\Controllers\Admin\ImageController::class, 'upload'])->name('images.upload');
    Route::delete('/images/delete', [App\Http\Controllers\Admin\ImageController::class, 'delete'])->name('images.delete');
    Route::get('/images', [App\Http\Controllers\Admin\ImageController::class, 'index'])->name('images.index');
    
    // Gestión de categorías
    Route::resource('categories', CategoryController::class);
    
    // Gestión de órdenes
    Route::resource('orders', OrderController::class);
    
    // Gestión de usuarios (clientes regulares)
    Route::resource('users', UserController::class);
    
    // Gestión de mayoristas
    Route::resource('wholesalers', WholesalerController::class);
    Route::post('wholesalers/{wholesaler}/approve', [WholesalerController::class, 'approve'])->name('wholesalers.approve');
    Route::post('wholesalers/{wholesaler}/toggle-status', [WholesalerController::class, 'toggleStatus'])->name('wholesalers.toggle-status');
    
    // Rutas específicas para modelo Wholesaler
    Route::post('wholesalers-model/{wholesaler}/activate', [WholesalerController::class, 'activateWholesaler'])->name('wholesalers-model.activate');
    Route::post('wholesalers-model/{wholesaler}/deactivate', [WholesalerController::class, 'deactivateWholesaler'])->name('wholesalers-model.deactivate');
    
    // Gestión de administradores
    Route::resource('admin-users', AdminUserController::class);
    
    // Gestión de transacciones de pago
    Route::resource('payment-transactions', PaymentTransactionController::class)->only(['index', 'show', 'destroy']);
    
    // Gestión de contactos
    Route::resource('contacts', ContactController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('contacts/bulk-resolve', [ContactController::class, 'bulkResolve'])->name('contacts.bulk-resolve');
    Route::get('contacts/new-count', [ContactController::class, 'getNewContactsCount'])->name('contacts.new-count');
    
    // Servir archivos de mayoristas
    Route::get('wholesalers/{wholesaler}/files/{filename}', [WholesalerController::class, 'serveFile'])->name('wholesalers.serve-file');
});

// Reset de contraseña (formularios web)
Route::get('/reset-password', function () {
    $token = request('token');
    if (!$token) {
        return redirect()->route('home')->with('error', 'Token de restablecimiento no válido');
    }
    
    // Verificar que el token sea válido
    if (!\App\Models\PasswordReset::isValidToken($token)) {
        \Illuminate\Support\Facades\Log::info("Token inválido o usado: {$token}");
        return view('auth.reset-password-expired');
    }
    
    // Marcar el token como usado inmediatamente al acceder al formulario
    \App\Models\PasswordReset::markAsUsed($token);
    
    return view('auth.reset-password', compact('token'));
})->name('password.reset.form');

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $token = $request->token;
    $password = $request->password;
    
    try {
        // Obtener el registro de reset (sin validar si está usado, ya que se marca como usado al acceder al formulario)
        $passwordReset = \App\Models\PasswordReset::where('token', $token)
            ->where('created_at', '>', now()->subHours(24)) // Solo validar que no haya expirado
            ->first();
            
        if (!$passwordReset) {
            \Illuminate\Support\Facades\Log::info("Token no encontrado o expirado: {$token}");
            return back()->with('error', 'El enlace de restablecimiento no es válido o ha expirado');
        }
        
        // Buscar el usuario
        $user = \App\Models\User::where('email', $passwordReset->email)->first();
        
        if (!$user) {
            return back()->with('error', 'Usuario no encontrado');
        }
        
        // Actualizar contraseña
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($password),
        ]);
        
        return redirect()->route('password.reset.success');
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Password reset error: ' . $e->getMessage());
        return back()->with('error', 'Error al restablecer la contraseña. Intenta nuevamente.');
    }
})->name('password.reset');

// Página de éxito después de restablecer contraseña
Route::get('/reset-password-success', function () {
    return view('auth.reset-password-success');
})->name('password.reset.success');

// Ruta de logout (placeholder)
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');
