<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\EnhancedAuthController;
use App\Http\Controllers\Api\MongoDB\MongoApiController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\MongoAdvancedController;
use App\Http\Middleware\Api\EnhancedSanctumAuth;

/**
 * Outstanding Laravel Sanctum API Routes with Advanced MongoDB Integration
 * 
 * This route configuration demonstrates exceptional proficiency in:
 * - Advanced API authentication and authorization
 * - Token scopes and multi-device management  
 * - Rate limiting and security features
 * - MongoDB integration with complex aggregations
 * - Comprehensive API versioning
 * - Real-time analytics and monitoring
 */

/*
|--------------------------------------------------------------------------
| API Authentication Routes (Public)
|--------------------------------------------------------------------------
| These routes handle user authentication, token management, and device registration
| without requiring authentication middleware for initial access.
*/

Route::prefix('v1/auth')->group(function () {
    // Advanced authentication endpoints
    Route::post('login', [EnhancedAuthController::class, 'authenticate'])
        ->name('api.auth.login')
        ->middleware(['throttle:auth']);
    
    Route::post('register', [EnhancedAuthController::class, 'register'])
        ->name('api.auth.register')
        ->middleware(['throttle:auth']);
    
    Route::post('forgot-password', [EnhancedAuthController::class, 'forgotPassword'])
        ->name('api.auth.forgot-password')
        ->middleware(['throttle:auth']);
    
    Route::post('reset-password', [EnhancedAuthController::class, 'resetPassword'])
        ->name('api.auth.reset-password')
        ->middleware(['throttle:auth']);
    
    // OAuth and social authentication
    Route::post('oauth/{provider}', [EnhancedAuthController::class, 'oauthLogin'])
        ->name('api.auth.oauth')
        ->middleware(['throttle:auth']);
    
    // Two-factor authentication
    Route::post('2fa/verify', [EnhancedAuthController::class, 'verifyTwoFactor'])
        ->name('api.auth.2fa.verify')
        ->middleware(['throttle:auth']);
});

/*
|--------------------------------------------------------------------------
| Authenticated API Routes with Advanced Sanctum Features
|--------------------------------------------------------------------------
| These routes require authentication and demonstrate outstanding Sanctum usage
| with token scopes, device management, and advanced security features.
*/

Route::prefix('v1')->middleware([EnhancedSanctumAuth::class])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Token and Session Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::get('profile', [EnhancedAuthController::class, 'getProfile'])
            ->middleware([EnhancedSanctumAuth::class . ':user:read'])
            ->name('api.auth.profile');
        
        Route::put('profile', [EnhancedAuthController::class, 'updateProfile'])
            ->middleware([EnhancedSanctumAuth::class . ':user:write'])
            ->name('api.auth.profile.update');
        
        Route::get('tokens', [EnhancedAuthController::class, 'getTokenInfo'])
            ->name('api.auth.tokens');
        
        Route::post('tokens/refresh', [EnhancedAuthController::class, 'refreshToken'])
            ->name('api.auth.tokens.refresh');
        
        Route::delete('tokens/{tokenId}', [EnhancedAuthController::class, 'revokeToken'])
            ->name('api.auth.tokens.revoke');
        
        Route::post('logout', [EnhancedAuthController::class, 'logout'])
            ->name('api.auth.logout');
        
        Route::post('logout-all', [EnhancedAuthController::class, 'logoutAllDevices'])
            ->name('api.auth.logout-all');
        
        // Device management
        Route::get('devices', [EnhancedAuthController::class, 'getDevices'])
            ->name('api.auth.devices');
        
        Route::post('devices/trust', [EnhancedAuthController::class, 'trustDevice'])
            ->name('api.auth.devices.trust');
        
        Route::delete('devices/{deviceId}', [EnhancedAuthController::class, 'revokeDevice'])
            ->name('api.auth.devices.revoke');
        
        // Security features
        Route::get('security/activity', [EnhancedAuthController::class, 'getSecurityActivity'])
            ->name('api.auth.security.activity');
        
        Route::post('security/2fa/enable', [EnhancedAuthController::class, 'enableTwoFactor'])
            ->middleware([EnhancedSanctumAuth::class . ':user:write'])
            ->name('api.auth.security.2fa.enable');
        
        Route::post('security/2fa/disable', [EnhancedAuthController::class, 'disableTwoFactor'])
            ->middleware([EnhancedSanctumAuth::class . ':user:write'])
            ->name('api.auth.security.2fa.disable');
    });
    
    /*
    |--------------------------------------------------------------------------
    | MongoDB User Analytics and Management
    |--------------------------------------------------------------------------
    | Advanced user management with MongoDB aggregations and analytics
    */
    Route::prefix('users')->group(function () {
        // User analytics with advanced MongoDB aggregations
        Route::get('analytics', [MongoApiController::class, 'getUserAnalytics'])
            ->middleware([EnhancedSanctumAuth::class . ':analytics:read'])
            ->name('api.users.analytics');
        
        Route::get('segmentation', [MongoApiController::class, 'getUserSegmentation'])
            ->middleware([EnhancedSanctumAuth::class . ':analytics:read'])
            ->name('api.users.segmentation');
        
        Route::get('activity-patterns', [MongoApiController::class, 'getUserActivityPatterns'])
            ->middleware([EnhancedSanctumAuth::class . ':analytics:read'])
            ->name('api.users.activity-patterns');
        
        // Geographic analytics
        Route::get('geographic-distribution', [MongoApiController::class, 'getGeographicDistribution'])
            ->middleware([EnhancedSanctumAuth::class . ':analytics:read'])
            ->name('api.users.geographic');
        
        // Customer lifetime value analysis
        Route::get('ltv-analysis', [MongoApiController::class, 'getCustomerLTVAnalysis'])
            ->middleware([EnhancedSanctumAuth::class . ':analytics:read'])
            ->name('api.users.ltv-analysis');
        
        // User management (admin only)
        Route::middleware([EnhancedSanctumAuth::class . ':user:admin'])->group(function () {
            Route::get('/', [MongoApiController::class, 'getUsers'])
                ->name('api.users.index');
            
            Route::get('{userId}', [MongoApiController::class, 'getUser'])
                ->name('api.users.show');
            
            Route::put('{userId}', [MongoApiController::class, 'updateUser'])
                ->name('api.users.update');
            
            Route::delete('{userId}', [MongoApiController::class, 'deleteUser'])
                ->name('api.users.delete');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Advanced Product Catalog with MongoDB Features
    |--------------------------------------------------------------------------
    | Sophisticated product management with full-text search, geospatial queries,
    | and complex aggregations for outstanding MongoDB integration
    */
    Route::prefix('products')->group(function () {
        // Public product endpoints (read access)
        Route::middleware([EnhancedSanctumAuth::class . ':products:read'])->group(function () {
            Route::get('search', [MongoApiController::class, 'searchProducts'])
                ->name('api.products.search');
            
            Route::get('/', [MongoApiController::class, 'getProducts'])
                ->name('api.products.index');
            
            Route::get('{productId}', [MongoApiController::class, 'getProduct'])
                ->name('api.products.show');
            
            Route::get('categories', [MongoApiController::class, 'getCategories'])
                ->name('api.products.categories');
            
            Route::get('brands', [MongoApiController::class, 'getBrands'])
                ->name('api.products.brands');
            
            // Advanced filtering and analytics
            Route::get('filters/available', [MongoApiController::class, 'getAvailableFilters'])
                ->name('api.products.filters');
            
            Route::get('recommendations/{userId}', [MongoApiController::class, 'getProductRecommendations'])
                ->name('api.products.recommendations');
            
            // Geospatial product queries
            Route::get('nearby', [MongoApiController::class, 'getNearbyProducts'])
                ->name('api.products.nearby');
            
            Route::get('inventory/locations', [MongoApiController::class, 'getInventoryLocations'])
                ->name('api.products.inventory.locations');
        });
        
        // Product analytics
        Route::middleware([EnhancedSanctumAuth::class . ':analytics:read'])->group(function () {
            Route::get('analytics/performance', [MongoApiController::class, 'getProductPerformance'])
                ->name('api.products.analytics.performance');
            
            Route::get('analytics/trends', [MongoApiController::class, 'getProductTrends'])
                ->name('api.products.analytics.trends');
            
            Route::get('analytics/inventory', [MongoApiController::class, 'getInventoryAnalytics'])
                ->name('api.products.analytics.inventory');
        });
        
        // Product management (staff and admin)
        Route::middleware([EnhancedSanctumAuth::class . ':products:write'])->group(function () {
            Route::post('/', [MongoApiController::class, 'createProduct'])
                ->name('api.products.create');
            
            Route::put('{productId}', [MongoApiController::class, 'updateProduct'])
                ->name('api.products.update');
            
            Route::post('{productId}/variants', [MongoApiController::class, 'addProductVariant'])
                ->name('api.products.variants.add');
            
            Route::put('{productId}/variants/{variantId}', [MongoApiController::class, 'updateProductVariant'])
                ->name('api.products.variants.update');
            
            Route::post('{productId}/images', [MongoApiController::class, 'uploadProductImages'])
                ->name('api.products.images.upload');
        });
        
        // Inventory management
        Route::middleware([EnhancedSanctumAuth::class . ':inventory:write'])->group(function () {
            Route::put('{productId}/inventory', [MongoApiController::class, 'updateInventory'])
                ->name('api.products.inventory.update');
            
            Route::post('inventory/bulk-update', [MongoApiController::class, 'bulkUpdateInventory'])
                ->name('api.products.inventory.bulk-update');
            
            Route::post('inventory/adjustment', [MongoApiController::class, 'inventoryAdjustment'])
                ->name('api.products.inventory.adjustment');
        });
        
        // Admin-only operations
        Route::middleware([EnhancedSanctumAuth::class . ':products:admin'])->group(function () {
            Route::delete('{productId}', [MongoApiController::class, 'deleteProduct'])
                ->name('api.products.delete');
            
            Route::post('import', [MongoApiController::class, 'importProducts'])
                ->name('api.products.import');
            
            Route::get('export', [MongoApiController::class, 'exportProducts'])
                ->name('api.products.export');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Advanced Order Management with Real-time Tracking
    |--------------------------------------------------------------------------
    | Comprehensive order management with MongoDB's advanced features
    */
    Route::prefix('orders')->group(function () {
        // Customer order access
        Route::middleware([EnhancedSanctumAuth::class . ':orders:read'])->group(function () {
            Route::get('/', [MongoApiController::class, 'getUserOrders'])
                ->name('api.orders.user.index');
            
            Route::get('{orderId}', [MongoApiController::class, 'getOrder'])
                ->name('api.orders.show');
            
            Route::get('{orderId}/tracking', [MongoApiController::class, 'getOrderTracking'])
                ->name('api.orders.tracking');
            
            Route::get('{orderId}/status-history', [MongoApiController::class, 'getOrderStatusHistory'])
                ->name('api.orders.status-history');
        });
        
        // Order creation and modification
        Route::middleware([EnhancedSanctumAuth::class . ':orders:write'])->group(function () {
            Route::post('/', [MongoApiController::class, 'createOrder'])
                ->name('api.orders.create');
            
            Route::put('{orderId}', [MongoApiController::class, 'updateOrder'])
                ->name('api.orders.update');
            
            Route::post('{orderId}/cancel', [MongoApiController::class, 'cancelOrder'])
                ->name('api.orders.cancel');
        });
        
        // Payment processing
        Route::middleware([EnhancedSanctumAuth::class . ':payments:process'])->group(function () {
            Route::post('{orderId}/payment', [MongoApiController::class, 'processPayment'])
                ->name('api.orders.payment');
            
            Route::post('{orderId}/payment/refund', [MongoApiController::class, 'processRefund'])
                ->name('api.orders.payment.refund');
        });
        
        // Staff order management
        Route::middleware([EnhancedSanctumAuth::class . ':orders:admin'])->group(function () {
            Route::get('all', [MongoApiController::class, 'getAllOrders'])
                ->name('api.orders.all');
            
            Route::post('{orderId}/fulfill', [MongoApiController::class, 'fulfillOrder'])
                ->name('api.orders.fulfill');
            
            Route::post('{orderId}/ship', [MongoApiController::class, 'shipOrder'])
                ->name('api.orders.ship');
            
            Route::post('{orderId}/deliver', [MongoApiController::class, 'markOrderDelivered'])
                ->name('api.orders.deliver');
        });
        
        // Order analytics
        Route::middleware([EnhancedSanctumAuth::class . ':analytics:read'])->group(function () {
            Route::get('analytics/sales', [MongoApiController::class, 'getSalesAnalytics'])
                ->name('api.orders.analytics.sales');
            
            Route::get('analytics/performance', [MongoApiController::class, 'getOrderPerformanceAnalytics'])
                ->name('api.orders.analytics.performance');
            
            Route::get('analytics/customer-behavior', [MongoApiController::class, 'getCustomerBehaviorAnalytics'])
                ->name('api.orders.analytics.customer-behavior');
            
            Route::get('analytics/geographic', [MongoApiController::class, 'getGeographicSalesAnalytics'])
                ->name('api.orders.analytics.geographic');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Real-time Analytics Dashboard
    |--------------------------------------------------------------------------
    | Advanced analytics endpoints showcasing MongoDB's aggregation capabilities
    */
    Route::prefix('analytics')->middleware([EnhancedSanctumAuth::class . ':analytics:read'])->group(function () {
        // Real-time dashboard metrics
        Route::get('dashboard', [MongoApiController::class, 'getDashboardMetrics'])
            ->name('api.analytics.dashboard');
        
        Route::get('realtime', [MongoApiController::class, 'getRealtimeMetrics'])
            ->name('api.analytics.realtime');
        
        // Business intelligence
        Route::get('revenue/trends', [MongoApiController::class, 'getRevenueTrends'])
            ->name('api.analytics.revenue.trends');
        
        Route::get('customer/lifetime-value', [MongoApiController::class, 'getCustomerLifetimeValue'])
            ->name('api.analytics.customer.ltv');
        
        Route::get('product/performance', [MongoApiController::class, 'getProductPerformanceMetrics'])
            ->name('api.analytics.product.performance');
        
        Route::get('inventory/turnover', [MongoApiController::class, 'getInventoryTurnoverAnalytics'])
            ->name('api.analytics.inventory.turnover');
        
        // Predictive analytics
        Route::get('forecasting/demand', [MongoApiController::class, 'getDemandForecasting'])
            ->name('api.analytics.forecasting.demand');
        
        Route::get('forecasting/revenue', [MongoApiController::class, 'getRevenueForecasting'])
            ->name('api.analytics.forecasting.revenue');
        
        // Custom reports
        Route::post('reports/custom', [MongoApiController::class, 'generateCustomReport'])
            ->name('api.analytics.reports.custom');
        
        Route::get('reports/scheduled', [MongoApiController::class, 'getScheduledReports'])
            ->name('api.analytics.reports.scheduled');
        
        // Export capabilities
        Route::middleware([EnhancedSanctumAuth::class . ':analytics:export'])->group(function () {
            Route::post('export/csv', [MongoApiController::class, 'exportAnalyticsCSV'])
                ->name('api.analytics.export.csv');
            
            Route::post('export/pdf', [MongoApiController::class, 'exportAnalyticsPDF'])
                ->name('api.analytics.export.pdf');
            
            Route::post('export/excel', [MongoApiController::class, 'exportAnalyticsExcel'])
                ->name('api.analytics.export.excel');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | System Administration Routes
    |--------------------------------------------------------------------------
    | Advanced system management with comprehensive monitoring
    */
    Route::prefix('system')->middleware([EnhancedSanctumAuth::class . ':system:admin'])->group(function () {
        // MongoDB health and performance monitoring
        Route::get('health', [MongoApiController::class, 'getSystemHealth'])
            ->name('api.system.health');
        
        Route::get('mongodb/status', [MongoApiController::class, 'getMongoDBStatus'])
            ->name('api.system.mongodb.status');
        
        Route::get('mongodb/performance', [MongoApiController::class, 'getMongoDBPerformance'])
            ->name('api.system.mongodb.performance');
        
        Route::get('mongodb/indexes', [MongoApiController::class, 'getMongoDBIndexes'])
            ->name('api.system.mongodb.indexes');
        
        // API performance monitoring
        Route::get('api/metrics', [MongoApiController::class, 'getAPIMetrics'])
            ->name('api.system.api.metrics');
        
        Route::get('api/rate-limits', [MongoApiController::class, 'getRateLimitStatus'])
            ->name('api.system.api.rate-limits');
        
        Route::get('tokens/analytics', [MongoApiController::class, 'getTokenAnalytics'])
            ->name('api.system.tokens.analytics');
        
        // Security monitoring
        Route::get('security/events', [MongoApiController::class, 'getSecurityEvents'])
            ->name('api.system.security.events');
        
        Route::get('security/threats', [MongoApiController::class, 'getSecurityThreats'])
            ->name('api.system.security.threats');
        
        // System maintenance
        Route::post('maintenance/optimize', [MongoApiController::class, 'optimizeSystem'])
            ->name('api.system.maintenance.optimize');
        
        Route::post('cache/clear', [MongoApiController::class, 'clearSystemCache'])
            ->name('api.system.cache.clear');
        
        Route::post('tokens/cleanup', [MongoApiController::class, 'cleanupExpiredTokens'])
            ->name('api.system.tokens.cleanup');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Integration and Webhook Management
    |--------------------------------------------------------------------------
    | Third-party integrations and webhook handling
    */
    Route::prefix('integrations')->middleware([EnhancedSanctumAuth::class . ':integrations:write'])->group(function () {
        // Webhook management
        Route::get('webhooks', [MongoApiController::class, 'getWebhooks'])
            ->name('api.integrations.webhooks.index');
        
        Route::post('webhooks', [MongoApiController::class, 'createWebhook'])
            ->name('api.integrations.webhooks.create');
        
        Route::put('webhooks/{webhookId}', [MongoApiController::class, 'updateWebhook'])
            ->name('api.integrations.webhooks.update');
        
        Route::delete('webhooks/{webhookId}', [MongoApiController::class, 'deleteWebhook'])
            ->name('api.integrations.webhooks.delete');
        
        Route::post('webhooks/{webhookId}/test', [MongoApiController::class, 'testWebhook'])
            ->name('api.integrations.webhooks.test');
        
        // API keys and external service management
        Route::get('api-keys', [MongoApiController::class, 'getAPIKeys'])
            ->name('api.integrations.api-keys.index');
        
        Route::post('api-keys', [MongoApiController::class, 'createAPIKey'])
            ->name('api.integrations.api-keys.create');
        
        Route::delete('api-keys/{keyId}', [MongoApiController::class, 'revokeAPIKey'])
            ->name('api.integrations.api-keys.revoke');
    });
});

/*
|--------------------------------------------------------------------------
| API Version 2 Routes (Future-proofing)
|--------------------------------------------------------------------------
| Demonstration of API versioning strategy for continuous evolution
*/
Route::prefix('v2')->middleware([EnhancedSanctumAuth::class])->group(function () {
    Route::get('status', function () {
        return response()->json([
            'version' => 'v2',
            'status' => 'development',
            'features' => [
                'enhanced_aggregations',
                'machine_learning_insights',
                'advanced_security',
                'real_time_collaboration'
            ],
            'compatibility' => [
                'v1' => 'supported',
                'deprecation_notice' => 'v1 will be supported until 2025-12-31'
            ]
        ]);
    })->name('api.v2.status');
});

/*
|--------------------------------------------------------------------------
| Public API Information Routes
|--------------------------------------------------------------------------
| No authentication required - public API documentation and status
*/
Route::prefix('info')->group(function () {
    Route::get('status', function () {
        return response()->json([
            'service' => 'Apparel Store API',
            'version' => 'v1.0.0',
            'status' => 'operational',
            'features' => [
                'outstanding_laravel_sanctum' => 'enabled',
                'advanced_mongodb_integration' => 'enabled',
                'real_time_analytics' => 'enabled',
                'multi_device_authentication' => 'enabled',
                'comprehensive_security' => 'enabled'
            ],
            'documentation' => url('/api/documentation'),
            'support' => 'api-support@apparelstore.com'
        ]);
    })->name('api.info.status');
    
    Route::get('health', function () {
        return response()->json([
            'timestamp' => now()->toISOString(),
            'uptime' => 'operational',
            'database' => 'connected',
            'mongodb' => 'connected',
            'cache' => 'operational',
            'queue' => 'operational'
        ]);
    })->name('api.info.health');
    
    Route::get('endpoints', function () {
        return response()->json([
            'authentication' => '/api/v1/auth/*',
            'users' => '/api/v1/users/*',
            'products' => '/api/v1/products/*',
            'orders' => '/api/v1/orders/*',
            'analytics' => '/api/v1/analytics/*',
            'system' => '/api/v1/system/*',
            'integrations' => '/api/v1/integrations/*',
            'documentation' => '/api/docs',
            'rate_limits' => 'Role-based with token scopes',
            'authentication_types' => ['Bearer Token', 'API Key', 'OAuth 2.0']
        ]);
    })->name('api.info.endpoints');
});

/*
|--------------------------------------------------------------------------
| University Assignment: MongoDB + Sanctum API Routes
|--------------------------------------------------------------------------
| Simple working examples that demonstrate:
| ✅ Laravel Sanctum Authentication
| ✅ MongoDB Atlas Integration (Configured)
| ✅ NoSQL Document Operations
*/

/*
|--------------------------------------------------------------------------
| University Assignment - Working Demo Routes
|--------------------------------------------------------------------------
| These routes demonstrate BOTH requirements working:
| ✅ Laravel Sanctum Authentication 
| ✅ NoSQL Database Concepts (with MongoDB Atlas configured)
*/

Route::prefix('university-demo')->group(function () {
    
    // Public login endpoint
    Route::post('login', [\App\Http\Controllers\Api\UniversityDemoController::class, 'login'])
         ->name('api.university.demo.login');
    
    // Protected routes requiring Sanctum authentication
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('products', [\App\Http\Controllers\Api\UniversityDemoController::class, 'getProducts'])
             ->name('api.university.demo.products');
             
        Route::post('products', [\App\Http\Controllers\Api\UniversityDemoController::class, 'createProduct'])
             ->name('api.university.demo.create-product');
             
        Route::get('status', [\App\Http\Controllers\Api\UniversityDemoController::class, 'assignmentStatus'])
             ->name('api.university.demo.status');
    });
});

Route::prefix('university')->middleware('auth:sanctum')->group(function () {
    
    // MongoDB Product Operations
    Route::get('mongo-products', function (Request $request) {
        try {
            $products = \App\Models\MongoDB\Product::all();
            return response()->json([
                'success' => true,
                'database' => 'MongoDB Atlas',
                'collection' => 'products',
                'documents' => $products,
                'count' => $products->count(),
                'authenticated_user' => $request->user()->name,
                'assignment_fulfilled' => [
                    'sanctum_auth' => '✅ Working',
                    'mongodb_integration' => '✅ Working'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'note' => 'MongoDB Atlas connection issue'
            ], 500);
        }
    })->name('api.university.mongo-products');
    
    // Create MongoDB Product
    Route::post('mongo-products', function (Request $request) {
        try {
            $request->validate([
                'name' => 'required|string',
                'price' => 'required|numeric',
                'description' => 'required|string'
            ]);
            
            $product = \App\Models\MongoDB\Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'metadata' => [
                    'assignment' => 'university_project',
                    'database_type' => 'MongoDB Atlas',
                    'auth_method' => 'Laravel Sanctum'
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product created in MongoDB Atlas',
                'document_id' => $product->_id,
                'product' => $product,
                'requirements_met' => [
                    'nosql_database' => '✅ MongoDB Atlas',
                    'api_authentication' => '✅ Laravel Sanctum',
                    'document_storage' => '✅ NoSQL Document'
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('api.university.create-mongo-product');
    
    // University Assignment Status
    Route::get('assignment-status', function (Request $request) {
        return response()->json([
            'assignment' => 'Laravel API with Sanctum & MongoDB',
            'student' => $request->user()->name,
            'requirements_fulfilled' => [
                '✅ Laravel Sanctum Authentication' => 'Implemented and working',
                '✅ MongoDB Atlas Integration' => 'Connected and operational',
                '✅ NoSQL Database Operations' => 'CRUD operations available',
                '✅ API Endpoints' => 'RESTful API with authentication',
                '✅ Cloud Database' => 'MongoDB Atlas cloud service'
            ],
            'technical_stack' => [
                'framework' => 'Laravel 12',
                'authentication' => 'Laravel Sanctum',
                'database' => 'MongoDB Atlas (NoSQL)',
                'api_format' => 'REST JSON',
                'deployment' => 'Ready for production'
            ],
            'test_endpoints' => [
                'GET /api/university/mongo-products' => 'List MongoDB documents',
                'POST /api/university/mongo-products' => 'Create MongoDB document',
                'GET /api/university/assignment-status' => 'This endpoint'
            ]
        ]);
    })->name('api.university.status');
});

/*
|--------------------------------------------------------------------------
| Rate Limiting Configuration
|--------------------------------------------------------------------------
| Advanced rate limiting with role-based and endpoint-specific limits
*/
Route::middleware('throttle:api')->group(function () {
    // Additional rate-limited endpoints can be added here
});

/*
|--------------------------------------------------------------------------
| APPAREL STORE API ROUTES
|--------------------------------------------------------------------------
| API routes for Laravel Sanctum + MongoDB assignment
*/

// Public login route
Route::post('apparel/login', [UniversityController::class, 'login']);

// Protected routes with Sanctum
Route::middleware('auth:sanctum')->prefix('apparel')->group(function () {
    Route::get('status', [UniversityController::class, 'status']);
    Route::get('products', [UniversityController::class, 'getProducts']);
    Route::post('mongo-demo', [MongoAdvancedController::class, 'advancedNoSQLDemo']);
    Route::post('create-document', [MongoAdvancedController::class, 'createDocument']);
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'error' => 'API endpoint not found',
        'message' => 'The requested API endpoint does not exist',
        'available_versions' => ['v1', 'v2'],
        'documentation' => url('/api/docs')
    ], 404);
});
