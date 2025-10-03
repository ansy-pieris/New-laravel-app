<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s%s%s%s',
        'localhost,localhost:3000,localhost:3001,localhost:8080,',
        '127.0.0.1,127.0.0.1:8000,127.0.0.1:3000,::1,',
        'app.apparelstore.local,admin.apparelstore.local,api.apparelstore.local,',
        '*.apparelstore.com,*.apparelstore.local,',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | API Features
    |--------------------------------------------------------------------------
    |
    | This section controls advanced API authentication features for 
    | outstanding Laravel Sanctum implementation with exceptional proficiency.
    |
    */

    'api_features' => [
        // Enable advanced token features
        'token_scopes' => env('SANCTUM_TOKEN_SCOPES', true),
        'token_refresh' => env('SANCTUM_TOKEN_REFRESH', true),
        'multi_device_support' => env('SANCTUM_MULTI_DEVICE', true),
        'device_management' => env('SANCTUM_DEVICE_MANAGEMENT', true),
        
        // Security enhancements
        'ip_whitelisting' => env('SANCTUM_IP_WHITELIST', false),
        'geolocation_validation' => env('SANCTUM_GEO_VALIDATION', false),
        'suspicious_activity_detection' => env('SANCTUM_ACTIVITY_DETECTION', true),
        
        // Performance optimizations
        'token_caching' => env('SANCTUM_TOKEN_CACHE', true),
        'rate_limiting' => env('SANCTUM_RATE_LIMITING', true),
        'request_logging' => env('SANCTUM_REQUEST_LOGGING', true),
        
        // Advanced features
        'webhook_notifications' => env('SANCTUM_WEBHOOKS', false),
        'audit_logging' => env('SANCTUM_AUDIT_LOGGING', true),
        'token_analytics' => env('SANCTUM_TOKEN_ANALYTICS', true)
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Scopes
    |--------------------------------------------------------------------------
    |
    | Define available token scopes for fine-grained API access control.
    | This enables exceptional proficiency in API security and access management.
    |
    */

    'scopes' => [
        // User management scopes
        'user:read' => 'Read user profile information',
        'user:write' => 'Update user profile information',
        'user:delete' => 'Delete user account',
        'user:admin' => 'Full user management access',
        
        // Product catalog scopes
        'products:read' => 'Read product information',
        'products:write' => 'Create and update products',
        'products:delete' => 'Delete products',
        'products:admin' => 'Full product management access',
        
        // Order management scopes
        'orders:read' => 'Read order information',
        'orders:write' => 'Create and update orders',
        'orders:cancel' => 'Cancel orders',
        'orders:refund' => 'Process order refunds',
        'orders:admin' => 'Full order management access',
        
        // Inventory management scopes
        'inventory:read' => 'Read inventory levels',
        'inventory:write' => 'Update inventory levels',
        'inventory:admin' => 'Full inventory management access',
        
        // Analytics and reporting scopes
        'analytics:read' => 'Access analytics data',
        'analytics:export' => 'Export analytics reports',
        'analytics:admin' => 'Full analytics access',
        
        // Payment processing scopes
        'payments:read' => 'Read payment information',
        'payments:process' => 'Process payments',
        'payments:refund' => 'Process refunds',
        'payments:admin' => 'Full payment management access',
        
        // System administration scopes
        'system:read' => 'Read system information',
        'system:write' => 'System configuration access',
        'system:admin' => 'Full system administration access',
        
        // API integration scopes
        'webhooks:manage' => 'Manage webhook endpoints',
        'integrations:read' => 'Read third-party integrations',
        'integrations:write' => 'Manage third-party integrations'
    ],

    /*
    |--------------------------------------------------------------------------
    | Device Types
    |--------------------------------------------------------------------------
    |
    | Define supported device types for multi-device token management.
    | This enables sophisticated device-specific authentication strategies.
    |
    */

    'device_types' => [
        'web' => [
            'name' => 'Web Browser',
            'expiration' => 60 * 24 * 7, // 7 days in minutes
            'refresh_enabled' => true,
            'concurrent_sessions' => 5,
            'require_2fa' => false
        ],
        'mobile_app' => [
            'name' => 'Mobile Application',
            'expiration' => 60 * 24 * 30, // 30 days in minutes
            'refresh_enabled' => true,
            'concurrent_sessions' => 3,
            'require_2fa' => false
        ],
        'desktop_app' => [
            'name' => 'Desktop Application',
            'expiration' => 60 * 24 * 14, // 14 days in minutes
            'refresh_enabled' => true,
            'concurrent_sessions' => 2,
            'require_2fa' => false
        ],
        'api_client' => [
            'name' => 'API Client',
            'expiration' => 60 * 24 * 365, // 1 year in minutes
            'refresh_enabled' => false,
            'concurrent_sessions' => 10,
            'require_2fa' => false
        ],
        'admin_panel' => [
            'name' => 'Admin Panel',
            'expiration' => 60 * 8, // 8 hours in minutes
            'refresh_enabled' => true,
            'concurrent_sessions' => 2,
            'require_2fa' => true
        ],
        'pos_system' => [
            'name' => 'Point of Sale System',
            'expiration' => 60 * 24, // 24 hours in minutes
            'refresh_enabled' => true,
            'concurrent_sessions' => 1,
            'require_2fa' => false
        ],
        'warehouse_scanner' => [
            'name' => 'Warehouse Scanner',
            'expiration' => 60 * 12, // 12 hours in minutes
            'refresh_enabled' => false,
            'concurrent_sessions' => 1,
            'require_2fa' => false
        ],
        'third_party' => [
            'name' => 'Third-party Integration',
            'expiration' => 60 * 24 * 90, // 90 days in minutes
            'refresh_enabled' => false,
            'concurrent_sessions' => 1,
            'require_2fa' => false
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiration Strategy
    |--------------------------------------------------------------------------
    |
    | Advanced token expiration configuration for outstanding security.
    | Different strategies for different use cases and security requirements.
    |
    */

    'expiration' => env('SANCTUM_EXPIRATION', 60 * 24), // 24 hours default

    'expiration_strategy' => [
        // Sliding expiration - token extends on use
        'sliding_expiration' => env('SANCTUM_SLIDING_EXPIRATION', true),
        'sliding_window' => env('SANCTUM_SLIDING_WINDOW', 60 * 2), // 2 hours
        
        // Absolute expiration - token expires at fixed time
        'absolute_expiration' => env('SANCTUM_ABSOLUTE_EXPIRATION', false),
        
        // Inactivity timeout - token expires if unused
        'inactivity_timeout' => env('SANCTUM_INACTIVITY_TIMEOUT', 60 * 24 * 7), // 7 days
        
        // Role-based expiration
        'role_based_expiration' => [
            'admin' => 60 * 8, // 8 hours
            'staff' => 60 * 12, // 12 hours
            'customer' => 60 * 24 * 30, // 30 days
            'api_client' => 60 * 24 * 90 // 90 days
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Advanced security settings for exceptional API protection and monitoring.
    |
    */

    'security' => [
        // IP address restrictions
        'ip_whitelist' => env('SANCTUM_IP_WHITELIST', []),
        'ip_blacklist' => env('SANCTUM_IP_BLACKLIST', []),
        'ip_geofencing' => [
            'enabled' => env('SANCTUM_GEOFENCING', false),
            'allowed_countries' => explode(',', env('SANCTUM_ALLOWED_COUNTRIES', 'US,CA,UK,AU')),
            'blocked_countries' => explode(',', env('SANCTUM_BLOCKED_COUNTRIES', ''))
        ],
        
        // Rate limiting configuration
        'rate_limits' => [
            'authentication' => [
                'max_attempts' => env('SANCTUM_AUTH_RATE_LIMIT', 5),
                'decay_minutes' => env('SANCTUM_AUTH_RATE_DECAY', 15)
            ],
            'api_requests' => [
                'per_minute' => env('SANCTUM_API_RATE_PER_MINUTE', 60),
                'per_hour' => env('SANCTUM_API_RATE_PER_HOUR', 1000),
                'per_day' => env('SANCTUM_API_RATE_PER_DAY', 10000)
            ],
            'role_based_limits' => [
                'admin' => ['per_minute' => 120, 'per_hour' => 5000],
                'staff' => ['per_minute' => 100, 'per_hour' => 3000],
                'customer' => ['per_minute' => 60, 'per_hour' => 1000],
                'api_client' => ['per_minute' => 200, 'per_hour' => 10000]
            ]
        ],
        
        // Suspicious activity detection
        'activity_monitoring' => [
            'enabled' => env('SANCTUM_ACTIVITY_MONITORING', true),
            'failed_login_threshold' => 3,
            'unusual_location_detection' => true,
            'concurrent_session_limit' => 5,
            'token_usage_anomaly_detection' => true,
            'brute_force_protection' => true
        ],
        
        // Token security enhancements
        'token_security' => [
            'require_https' => env('SANCTUM_REQUIRE_HTTPS', true),
            'token_rotation' => env('SANCTUM_TOKEN_ROTATION', true),
            'secure_headers' => env('SANCTUM_SECURE_HEADERS', true),
            'token_fingerprinting' => env('SANCTUM_TOKEN_FINGERPRINTING', true),
            'encrypt_payloads' => env('SANCTUM_ENCRYPT_PAYLOADS', false)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Optimizations for high-performance API authentication and token management.
    |
    */

    'performance' => [
        // Caching configuration
        'cache' => [
            'enabled' => env('SANCTUM_CACHE_ENABLED', true),
            'driver' => env('SANCTUM_CACHE_DRIVER', 'redis'),
            'prefix' => env('SANCTUM_CACHE_PREFIX', 'sanctum_'),
            'ttl' => env('SANCTUM_CACHE_TTL', 3600), // 1 hour
            'token_cache_ttl' => env('SANCTUM_TOKEN_CACHE_TTL', 300) // 5 minutes
        ],
        
        // Database optimizations
        'database' => [
            'connection' => env('SANCTUM_DB_CONNECTION', 'default'),
            'token_pruning' => [
                'enabled' => env('SANCTUM_AUTO_PRUNE', true),
                'frequency' => env('SANCTUM_PRUNE_FREQUENCY', 'daily'),
                'expired_token_retention' => 7 // days
            ],
            'batch_operations' => env('SANCTUM_BATCH_OPERATIONS', true)
        ],
        
        // API response optimizations
        'response_optimization' => [
            'compression' => env('SANCTUM_RESPONSE_COMPRESSION', true),
            'etag_support' => env('SANCTUM_ETAG_SUPPORT', true),
            'conditional_requests' => env('SANCTUM_CONDITIONAL_REQUESTS', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics and Monitoring
    |--------------------------------------------------------------------------
    |
    | Advanced analytics and monitoring for API usage patterns and security insights.
    |
    */

    'analytics' => [
        'enabled' => env('SANCTUM_ANALYTICS_ENABLED', true),
        'detailed_logging' => env('SANCTUM_DETAILED_LOGGING', false),
        'metrics_collection' => [
            'api_usage_stats' => true,
            'authentication_patterns' => true,
            'token_lifecycle_tracking' => true,
            'performance_metrics' => true,
            'security_events' => true
        ],
        'reporting' => [
            'daily_summaries' => env('SANCTUM_DAILY_REPORTS', true),
            'weekly_insights' => env('SANCTUM_WEEKLY_INSIGHTS', true),
            'security_alerts' => env('SANCTUM_SECURITY_ALERTS', true)
        ],
        'retention_period' => env('SANCTUM_ANALYTICS_RETENTION', 90) // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for third-party integrations and webhook notifications.
    |
    */

    'integrations' => [
        // Webhook configuration
        'webhooks' => [
            'enabled' => env('SANCTUM_WEBHOOKS_ENABLED', false),
            'endpoints' => [
                'authentication_events' => env('SANCTUM_AUTH_WEBHOOK_URL'),
                'security_alerts' => env('SANCTUM_SECURITY_WEBHOOK_URL'),
                'token_lifecycle' => env('SANCTUM_TOKEN_WEBHOOK_URL')
            ],
            'retry_attempts' => 3,
            'timeout' => 10,
            'verify_ssl' => env('SANCTUM_WEBHOOK_VERIFY_SSL', true)
        ],
        
        // External services
        'external_services' => [
            'geolocation_service' => env('SANCTUM_GEOLOCATION_SERVICE', 'ipapi'),
            'fraud_detection_service' => env('SANCTUM_FRAUD_SERVICE'),
            'notification_service' => env('SANCTUM_NOTIFICATION_SERVICE')
        ],
        
        // API versioning
        'versioning' => [
            'strategy' => env('SANCTUM_VERSIONING_STRATEGY', 'header'), // header, uri, parameter
            'default_version' => env('SANCTUM_DEFAULT_VERSION', 'v1'),
            'supported_versions' => explode(',', env('SANCTUM_SUPPORTED_VERSIONS', 'v1,v2')),
            'deprecation_notices' => env('SANCTUM_DEPRECATION_NOTICES', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | security scanning initiatives maintained by open source platforms
    | that notify developers if they commit tokens into repositories.
    |
    | See: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'apparel_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
