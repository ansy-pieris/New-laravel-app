<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MongoAdvancedController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomepageApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| APPAREL STORE API ROUTES
|--------------------------------------------------------------------------
| Complete e-commerce API with Laravel Sanctum + MongoDB
*/

// ============================================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================================

Route::prefix('apparel')->group(function () {
    
    // ========================================
    // HOMEPAGE DATA
    // ========================================
    Route::get('homepage', [HomepageApiController::class, 'index']);        // Homepage data
    Route::get('home', [HomepageApiController::class, 'index']);            // Alternative homepage route
    
    // ========================================
    // AUTHENTICATION
    // ========================================
    Route::post('login', [AuthController::class, 'login']);        // User login
    Route::post('register', [AuthController::class, 'register']);  // User registration
    
    // ========================================
    // PRODUCT BROWSING
    // ========================================
    Route::get('products', [ProductApiController::class, 'index']);         // Get all products
    Route::get('products/search', [ProductApiController::class, 'search']); // Search products (specific routes first)
    Route::get('products/featured', [ProductApiController::class, 'featured']); // Featured products (specific routes first)
    Route::get('featured-test', [ProductApiController::class, 'featured']); // Test route for featured products
    Route::get('products/{id}', [ProductApiController::class, 'show']);     // Get single product (general route last)
    
    // ========================================
    // CATEGORY BROWSING
    // ========================================
    Route::get('categories', [CategoryApiController::class, 'index']);              // Get all categories
    Route::get('categories/{slug}/page', [ProductApiController::class, 'byCategory']); // Category page data (must be before {id} routes)
    Route::get('categories/{id}', [CategoryApiController::class, 'show']);          // Get single category
    Route::get('categories/{id}/products', [CategoryApiController::class, 'getProducts']); // Category products
    Route::get('categories/{id}/stats', [CategoryApiController::class, 'getStats']);       // Category statistics
    
    // ========================================
    // USER BROWSING
    // ========================================
    Route::get('users', [UserController::class, 'index']);                         // Get all users
    Route::get('users/{id}', [UserController::class, 'show']);                     // Get single user
});

// ============================================================================
// PROTECTED ROUTES (Authentication Required - Bearer Token)
// ============================================================================

Route::middleware('auth:sanctum')->prefix('apparel')->group(function () {
    
    // ========================================
    // USER MANAGEMENT
    // ========================================
    Route::get('profile', [AuthController::class, 'getProfile']);      // Get user profile
    Route::put('profile', [AuthController::class, 'updateProfile']);   // Update profile
    Route::post('logout', [AuthController::class, 'logout']);          // Logout user
    Route::get('status', [AuthController::class, 'status']);           // API status
    
    // ========================================
    // SHOPPING CART
    // ========================================
    Route::get('cart', [CartApiController::class, 'index']);                 // View cart
    Route::get('cart/count', [CartApiController::class, 'getCartCount']);    // Cart item count
    Route::post('cart/add', [CartApiController::class, 'addToCart']);        // Add to cart
    Route::put('cart/update', [CartApiController::class, 'updateQuantity']); // Update quantity (renamed for clarity)
    Route::delete('cart/remove', [CartApiController::class, 'removeFromCart']); // Remove item (renamed for clarity)
    Route::delete('cart/clear', [CartApiController::class, 'clearCart']);    // Clear cart
    
    // ========================================
    // ORDERS
    // ========================================
    Route::post('checkout', [OrderApiController::class, 'checkout']);            // Process checkout
    Route::get('orders', [OrderApiController::class, 'getUserOrders']);          // Order history
    Route::get('orders/{id}', [OrderApiController::class, 'getOrderDetails']);   // Order details
    Route::put('orders/{id}/cancel', [OrderApiController::class, 'cancelOrder']); // Cancel order
    Route::get('track/{orderNumber}', [OrderApiController::class, 'trackOrder']); // Track order
    
    // ========================================
    // ADMIN - PRODUCT MANAGEMENT
    // ========================================
    Route::post('admin/products', [ProductApiController::class, 'store']);      // Create product
    Route::put('admin/products/{id}', [ProductApiController::class, 'update']); // Update product
    Route::delete('admin/products/{id}', [ProductApiController::class, 'destroy']); // Delete product
    
    // ========================================
    // ADMIN - CATEGORY MANAGEMENT
    // ========================================
    Route::post('admin/categories', [CategoryApiController::class, 'store']);      // Create category
    Route::put('admin/categories/{id}', [CategoryApiController::class, 'update']); // Update category
    Route::delete('admin/categories/{id}', [CategoryApiController::class, 'destroy']); // Delete category
    
    // ========================================
    // UNIVERSITY ASSIGNMENT - MONGODB DEMOS
    // ========================================
    Route::post('mongo-demo', [MongoAdvancedController::class, 'advancedNoSQLDemo']); // MongoDB aggregation
    Route::post('create-document', [MongoAdvancedController::class, 'createDocument']); // Create MongoDB doc
});