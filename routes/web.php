<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
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
    
    // Gestión de productos
    Route::resource('products', ProductController::class);
    
    // Gestión de categorías
    Route::resource('categories', CategoryController::class);
    
    // Gestión de órdenes
    Route::resource('orders', OrderController::class);
    
    // Gestión de usuarios (clientes)
    Route::resource('users', UserController::class);
    
    // Gestión de administradores
    Route::resource('admin-users', AdminUserController::class);
});

// Ruta de logout (placeholder)
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');
