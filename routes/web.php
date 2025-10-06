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
    
    // Gestión de administradores
    Route::resource('admin-users', AdminUserController::class);
    
    // Gestión de transacciones de pago
    Route::resource('payment-transactions', PaymentTransactionController::class)->only(['index', 'show', 'destroy']);
    
    // Gestión de contactos
    Route::resource('contacts', ContactController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('contacts/bulk-resolve', [ContactController::class, 'bulkResolve'])->name('contacts.bulk-resolve');
    Route::get('contacts/new-count', [ContactController::class, 'getNewContactsCount'])->name('contacts.new-count');
});

// Ruta de logout (placeholder)
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');
