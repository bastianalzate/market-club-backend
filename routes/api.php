<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\WishlistController;
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
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Rutas de categorías públicas
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Rutas de búsqueda públicas
Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
Route::get('/search/featured', [SearchController::class, 'featured']);
Route::get('/search/related/{product}', [SearchController::class, 'related']);

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
    
    // Carrito de compras
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addProduct']);
    Route::put('/cart/update', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/remove', [CartController::class, 'removeProduct']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::get('/cart/summary', [CartController::class, 'summary']);
    Route::post('/cart/sync', [CartController::class, 'sync']);
    
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/add', [WishlistController::class, 'addProduct']);
    Route::delete('/wishlist/remove', [WishlistController::class, 'removeProduct']);
    Route::post('/wishlist/check', [WishlistController::class, 'checkProduct']);
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear']);
    Route::post('/wishlist/move-to-cart', [WishlistController::class, 'moveToCart']);
    
    // Checkout
    Route::get('/checkout/summary', [CheckoutController::class, 'getCheckoutSummary']);
    Route::post('/checkout/validate-address', [CheckoutController::class, 'validateShippingAddress']);
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping']);
    Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder']);
    
    // Rutas de pago protegidas
    Route::post('/payments/process', [PaymentController::class, 'processPayment']);
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);
    
    // Rutas de administración (requieren autenticación)
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });
});
