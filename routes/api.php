<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\WholesalerCartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\PrivateFileController;
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
Route::get('/products/latest-beers', [ProductController::class, 'latestBeers']);
Route::get('/products/filters', [ProductController::class, 'getFilters']);
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
Route::post('/payments/wompi/create-session', [PaymentController::class, 'createWompiSession']);
Route::post('/payments/wompi/create-widget', [PaymentController::class, 'createWompiWidget']);
Route::post('/payments/wompi/create-checkout', [PaymentController::class, 'createWompiCheckout']);
Route::post('/payments/check-status', [PaymentController::class, 'checkPaymentStatus']);
Route::get('/payments/wompi/config', [PaymentController::class, 'checkWompiConfig']);
Route::post('/payments/wompi/generate-signature', [PaymentController::class, 'getWidgetSignature']);
Route::post('/payments/update-order-status', [PaymentController::class, 'updateOrderStatus']);
Route::post('/payments/create-transaction', [PaymentController::class, 'createPaymentTransaction']);

// Rutas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payments/wompi/generate-subscription-signature', [PaymentController::class, 'getSubscriptionSignature']);
    Route::post('/payments/process-subscription', [PaymentController::class, 'processSubscriptionPayment']);
    
    // Rutas de suscripciones
    Route::get('/subscriptions/current', [SubscriptionController::class, 'getCurrentSubscription']);
    Route::get('/subscriptions/history', [SubscriptionController::class, 'getSubscriptionHistory']);
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancelSubscription']);
    Route::post('/subscriptions/reactivate', [SubscriptionController::class, 'reactivateSubscription']);
    Route::put('/subscriptions/payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
});

// Webhook de Wompi (sin autenticación)
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// Contacto (público)
Route::post('/contact', [ContactController::class, 'store']);

// Archivos privados (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/private-file/{path}', [PrivateFileController::class, 'serve'])->where('path', '.*');
    Route::get('/private-file/{path}/download', [PrivateFileController::class, 'download'])->where('path', '.*');
});

// Planes de suscripción (público)
Route::get('/subscriptions/plans', [SubscriptionController::class, 'getAvailablePlans']);

// Carrito de compras (público - funciona con session_id)
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'addProduct']);
Route::post('/cart/add-gift', [CartController::class, 'addGift']);
Route::put('/cart/update', [CartController::class, 'updateQuantity']);
Route::delete('/cart/remove', [CartController::class, 'removeProduct']);
Route::delete('/cart/clear', [CartController::class, 'clear']);
Route::get('/cart/summary', [CartController::class, 'summary']);

// Carrito de mayoristas (público - funciona con session_id)
Route::get('/wholesaler/cart', [WholesalerCartController::class, 'index']);
Route::post('/wholesaler/cart/add', [WholesalerCartController::class, 'addProduct']);
Route::put('/wholesaler/cart/update', [WholesalerCartController::class, 'updateQuantity']);
Route::delete('/wholesaler/cart/remove', [WholesalerCartController::class, 'removeProduct']);
Route::delete('/wholesaler/cart/clear', [WholesalerCartController::class, 'clear']);
Route::get('/wholesaler/cart/summary', [WholesalerCartController::class, 'summary']);
Route::post('/wholesaler/cart/discount', [WholesalerCartController::class, 'applyDiscount']);
Route::post('/wholesaler/cart/notes', [WholesalerCartController::class, 'addNotes']);

// Checkout (público - funciona con session_id)
Route::get('/checkout/summary', [CheckoutController::class, 'getCheckoutSummary']);
Route::post('/checkout/validate-address', [CheckoutController::class, 'validateShippingAddress']);
Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping']);
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder']);
Route::post('/checkout/sync-cart', [CheckoutController::class, 'syncCartAfterLogin']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Perfil de usuario
    Route::get('/user/profile', [UserProfileController::class, 'getProfile']);
    Route::put('/user/profile', [UserProfileController::class, 'updateProfile']);
    Route::put('/user/password', [UserProfileController::class, 'changePassword']);
    Route::get('/user/orders', [UserProfileController::class, 'getOrders']);
    Route::get('/user/orders/{orderId}', [UserProfileController::class, 'getOrder']);
    Route::get('/user/favorites', [UserProfileController::class, 'getFavorites']);
    Route::post('/user/favorites/{productId}', [UserProfileController::class, 'addFavorite']);
    Route::delete('/user/favorites/{productId}', [UserProfileController::class, 'removeFavorite']);
    Route::get('/user/settings', [UserProfileController::class, 'getSettings']);
    Route::put('/user/settings', [UserProfileController::class, 'updateSettings']);
    
    // Suscripciones
    Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscriptions/current', [SubscriptionController::class, 'getCurrentSubscription']);
    Route::get('/subscriptions/history', [SubscriptionController::class, 'getSubscriptionHistory']);
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancelSubscription']);
    Route::post('/subscriptions/renew', [SubscriptionController::class, 'renewSubscription']);
    
    // Gestión de métodos de pago y renovación automática
    Route::post('/subscriptions/payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
    Route::delete('/subscriptions/payment-method', [SubscriptionController::class, 'removePaymentMethod']);
    Route::post('/subscriptions/auto-renew', [SubscriptionController::class, 'toggleAutoRenew']);
    Route::post('/subscriptions/reactivate', [SubscriptionController::class, 'reactivateSubscription']);
    
    // Órdenes del usuario (alias para compatibilidad)
    Route::apiResource('orders', OrderController::class);
    
    // Sincronización de carrito (solo para usuarios autenticados)
    Route::post('/cart/sync', [CartController::class, 'sync']);
    
    // Sincronización de carrito de mayoristas (solo para mayoristas autenticados)
    Route::post('/wholesaler/cart/sync', [WholesalerCartController::class, 'sync']);
    
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/add', [WishlistController::class, 'addProduct']);
    Route::delete('/wishlist/remove', [WishlistController::class, 'removeProduct']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::post('/wishlist/check', [WishlistController::class, 'checkProduct']);
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear']);
    Route::post('/wishlist/move-to-cart', [WishlistController::class, 'moveToCart']);
    
    // Rutas de pago protegidas
    Route::post('/payments/process', [PaymentController::class, 'processPayment']);
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);
    
    // Rutas de administración (requieren autenticación)
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });
});
