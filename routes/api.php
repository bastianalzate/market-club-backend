<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas de productos públicas
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Rutas de categorías públicas
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Rutas de pago públicas
Route::post('/payments/token', [PaymentController::class, 'createPaymentToken']);
Route::get('/payments/methods', [PaymentController::class, 'getPaymentMethods']);

// Webhook de Wompi (sin autenticación)
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Órdenes del usuario
    Route::apiResource('orders', OrderController::class);
    
    // Rutas de pago protegidas
    Route::post('/payments/process', [PaymentController::class, 'processPayment']);
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);
    
    // Rutas de administración (requieren autenticación)
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });
});
