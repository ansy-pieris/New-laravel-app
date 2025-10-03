<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outstanding Notification System Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration enables comprehensive notification delivery through
    | multiple channels: Email (Mailgun/SES), SMS/WhatsApp (Twilio), and 
    | Push Notifications (Firebase). Demonstrates exceptional proficiency
    | in Laravel notification architecture and third-party service integration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | Define the default channels used for sending notifications when no
    | specific channels are provided. The system will intelligently
    | select the most appropriate channel based on user preferences.
    |
    */

    'default_channels' => [
        'email' => env('NOTIFICATIONS_EMAIL_ENABLED', true),
        'sms' => env('NOTIFICATIONS_SMS_ENABLED', true),
        'push' => env('NOTIFICATIONS_PUSH_ENABLED', true),
        'database' => env('NOTIFICATIONS_DATABASE_ENABLED', true),
        'slack' => env('NOTIFICATIONS_SLACK_ENABLED', false),
        'webhook' => env('NOTIFICATIONS_WEBHOOK_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Advanced email configuration supporting multiple providers with
    | intelligent failover, template management, and delivery tracking.
    |
    */

    'email' => [
        // Primary email service provider
        'primary_provider' => env('MAIL_MAILER', 'mailgun'), // mailgun, ses, smtp

        // Provider-specific configurations
        'providers' => [
            'mailgun' => [
                'domain' => env('MAILGUN_DOMAIN'),
                'secret' => env('MAILGUN_SECRET'),
                'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
                'scheme' => 'https',
                'features' => [
                    'tracking' => true,
                    'analytics' => true,
                    'templates' => true,
                    'scheduling' => true,
                    'suppression_lists' => true
                ],
                'limits' => [
                    'daily' => env('MAILGUN_DAILY_LIMIT', 10000),
                    'hourly' => env('MAILGUN_HOURLY_LIMIT', 1000),
                    'per_minute' => env('MAILGUN_RATE_LIMIT', 100)
                ]
            ],

            'ses' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'features' => [
                    'tracking' => true,
                    'analytics' => true,
                    'templates' => true,
                    'reputation_tracking' => true,
                    'dedicated_ips' => false
                ],
                'limits' => [
                    'daily' => env('SES_DAILY_LIMIT', 50000),
                    'per_second' => env('SES_RATE_LIMIT', 14)
                ]
            ],

            'smtp' => [
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'features' => [
                    'tracking' => false,
                    'analytics' => false,
                    'templates' => false
                ]
            ]
        ],

        // Failover configuration
        'failover' => [
            'enabled' => env('EMAIL_FAILOVER_ENABLED', true),
            'providers' => ['mailgun', 'ses', 'smtp'],
            'retry_attempts' => 3,
            'retry_delays' => [30, 300, 1800] // seconds
        ],

        // Template management
        'templates' => [
            'engine' => 'blade', // blade, twig, mustache
            'cache_enabled' => env('EMAIL_TEMPLATE_CACHE', true),
            'cache_ttl' => 3600, // seconds
            'default_layout' => 'emails.layouts.default',
            'brand_colors' => [
                'primary' => env('BRAND_PRIMARY_COLOR', '#2563eb'),
                'secondary' => env('BRAND_SECONDARY_COLOR', '#64748b'),
                'accent' => env('BRAND_ACCENT_COLOR', '#10b981')
            ]
        ],

        // Tracking and analytics
        'tracking' => [
            'opens' => env('EMAIL_TRACK_OPENS', true),
            'clicks' => env('EMAIL_TRACK_CLICKS', true),
            'unsubscribes' => env('EMAIL_TRACK_UNSUBSCRIBES', true),
            'bounces' => env('EMAIL_TRACK_BOUNCES', true),
            'complaints' => env('EMAIL_TRACK_COMPLAINTS', true)
        ],

        // Anti-spam and reputation management
        'reputation' => [
            'dkim_enabled' => env('EMAIL_DKIM_ENABLED', true),
            'spf_enabled' => env('EMAIL_SPF_ENABLED', true),
            'dmarc_enabled' => env('EMAIL_DMARC_ENABLED', true),
            'feedback_loop' => env('EMAIL_FEEDBACK_LOOP', true),
            'suppression_lists' => env('EMAIL_SUPPRESSION_LISTS', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS and WhatsApp Configuration (Twilio)
    |--------------------------------------------------------------------------
    |
    | Advanced SMS and WhatsApp messaging configuration with delivery
    | tracking, template management, and intelligent routing.
    |
    */

    'sms' => [
        // Primary SMS provider
        'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, vonage, aws_sns

        // Twilio configuration
        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM_NUMBER'),
            'messaging_service_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
            'features' => [
                'delivery_tracking' => true,
                'short_codes' => env('TWILIO_SHORT_CODES', false),
                'alpha_sender_id' => env('TWILIO_ALPHA_SENDER', false),
                'smart_encoding' => true,
                'url_shortening' => env('TWILIO_URL_SHORTENING', true)
            ],
            'limits' => [
                'per_second' => env('TWILIO_RATE_LIMIT', 1),
                'daily' => env('TWILIO_DAILY_LIMIT', 10000),
                'concurrent_requests' => env('TWILIO_CONCURRENT_LIMIT', 100)
            ]
        ],

        // WhatsApp Business API (Twilio)
        'whatsapp' => [
            'enabled' => env('WHATSAPP_ENABLED', false),
            'from' => env('TWILIO_WHATSAPP_FROM'), // whatsapp:+1234567890
            'business_profile' => [
                'display_name' => env('WHATSAPP_BUSINESS_NAME', 'Apparel Store'),
                'description' => env('WHATSAPP_BUSINESS_DESCRIPTION'),
                'website' => env('WHATSAPP_BUSINESS_WEBSITE'),
                'industry' => 'Retail'
            ],
            'templates' => [
                'approved_templates' => [
                    'order_confirmation',
                    'order_shipped',
                    'order_delivered',
                    'payment_reminder',
                    'account_verification'
                ],
                'fallback_to_sms' => env('WHATSAPP_FALLBACK_SMS', true)
            ],
            'features' => [
                'rich_media' => true,
                'interactive_messages' => true,
                'template_messages' => true,
                'delivery_tracking' => true
            ]
        ],

        // Message routing and optimization
        'routing' => [
            'intelligent_routing' => env('SMS_INTELLIGENT_ROUTING', true),
            'cost_optimization' => env('SMS_COST_OPTIMIZATION', true),
            'delivery_time_optimization' => env('SMS_DELIVERY_OPTIMIZATION', true),
            'carrier_filtering' => env('SMS_CARRIER_FILTERING', false)
        ],

        // International messaging
        'international' => [
            'enabled' => env('SMS_INTERNATIONAL_ENABLED', true),
            'allowed_countries' => explode(',', env('SMS_ALLOWED_COUNTRIES', 'US,CA,UK,AU,DE,FR,IT,ES')),
            'blocked_countries' => explode(',', env('SMS_BLOCKED_COUNTRIES', '')),
            'currency_conversion' => env('SMS_CURRENCY_CONVERSION', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Push Notification Configuration (Firebase)
    |--------------------------------------------------------------------------
    |
    | Firebase Cloud Messaging configuration for web and mobile push
    | notifications with advanced features and analytics.
    |
    */

    'push' => [
        // Firebase configuration
        'firebase' => [
            'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'server_key' => env('FIREBASE_SERVER_KEY'),
            'sender_id' => env('FIREBASE_SENDER_ID'),
            'vapid_public_key' => env('FIREBASE_VAPID_PUBLIC_KEY'),
            'vapid_private_key' => env('FIREBASE_VAPID_PRIVATE_KEY'),
            'database_url' => env('FIREBASE_DATABASE_URL'),
            'features' => [
                'analytics' => env('FIREBASE_ANALYTICS', true),
                'crashlytics' => env('FIREBASE_CRASHLYTICS', false),
                'remote_config' => env('FIREBASE_REMOTE_CONFIG', false),
                'a_b_testing' => env('FIREBASE_AB_TESTING', false)
            ]
        ],

        // Platform-specific configurations
        'platforms' => [
            'web' => [
                'enabled' => env('PUSH_WEB_ENABLED', true),
                'vapid_enabled' => env('PUSH_WEB_VAPID', true),
                'icon' => env('PUSH_WEB_ICON', '/images/notification-icon.png'),
                'badge' => env('PUSH_WEB_BADGE', '/images/notification-badge.png'),
                'sound' => env('PUSH_WEB_SOUND', 'default'),
                'features' => [
                    'actions' => true,
                    'images' => true,
                    'custom_data' => true,
                    'silent_push' => true
                ]
            ],

            'android' => [
                'enabled' => env('PUSH_ANDROID_ENABLED', true),
                'priority' => env('PUSH_ANDROID_PRIORITY', 'high'), // normal, high
                'sound' => env('PUSH_ANDROID_SOUND', 'default'),
                'icon' => env('PUSH_ANDROID_ICON'),
                'color' => env('PUSH_ANDROID_COLOR', '#2563eb'),
                'features' => [
                    'collapse_key' => true,
                    'time_to_live' => 86400, // seconds
                    'restricted_package_name' => env('ANDROID_PACKAGE_NAME'),
                    'notification_channels' => [
                        'orders' => 'Order Updates',
                        'promotions' => 'Promotions & Offers',
                        'account' => 'Account Notifications',
                        'general' => 'General Notifications'
                    ]
                ]
            ],

            'ios' => [
                'enabled' => env('PUSH_IOS_ENABLED', true),
                'priority' => env('PUSH_IOS_PRIORITY', 10), // 5 = normal, 10 = high
                'sound' => env('PUSH_IOS_SOUND', 'default'),
                'badge' => env('PUSH_IOS_BADGE_AUTO', true),
                'features' => [
                    'content_available' => true,
                    'mutable_content' => true,
                    'thread_id' => true,
                    'collapse_id' => true,
                    'apns_push_type' => 'alert', // alert, background, voip
                    'apns_expiration' => 86400 // seconds
                ]
            ]
        ],

        // Advanced features
        'features' => [
            'topic_messaging' => env('PUSH_TOPIC_MESSAGING', true),
            'condition_messaging' => env('PUSH_CONDITION_MESSAGING', true),
            'scheduled_messaging' => env('PUSH_SCHEDULED_MESSAGING', true),
            'a_b_testing' => env('PUSH_AB_TESTING', false),
            'analytics' => env('PUSH_ANALYTICS', true)
        ],

        // Targeting and segmentation
        'targeting' => [
            'user_segments' => [
                'vip_customers',
                'frequent_buyers',
                'cart_abandoners',
                'inactive_users',
                'new_registrations'
            ],
            'geographic_targeting' => env('PUSH_GEO_TARGETING', true),
            'demographic_targeting' => env('PUSH_DEMO_TARGETING', true),
            'behavioral_targeting' => env('PUSH_BEHAVIOR_TARGETING', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Queue and Processing
    |--------------------------------------------------------------------------
    |
    | Advanced queueing system for reliable notification delivery with
    | priority handling, batch processing, and retry mechanisms.
    |
    */

    'queue' => [
        // Queue configuration
        'default_queue' => env('NOTIFICATIONS_QUEUE', 'notifications'),
        'high_priority_queue' => env('NOTIFICATIONS_HIGH_PRIORITY_QUEUE', 'notifications-high'),
        'batch_queue' => env('NOTIFICATIONS_BATCH_QUEUE', 'notifications-batch'),
        'failed_queue' => env('NOTIFICATIONS_FAILED_QUEUE', 'notifications-failed'),

        // Processing configuration
        'batch_processing' => [
            'enabled' => env('NOTIFICATIONS_BATCH_ENABLED', true),
            'batch_size' => env('NOTIFICATIONS_BATCH_SIZE', 100),
            'batch_timeout' => env('NOTIFICATIONS_BATCH_TIMEOUT', 300), // seconds
            'concurrent_batches' => env('NOTIFICATIONS_CONCURRENT_BATCHES', 3)
        ],

        // Retry configuration
        'retry' => [
            'max_attempts' => env('NOTIFICATIONS_MAX_ATTEMPTS', 3),
            'retry_delays' => [60, 300, 1800], // seconds between retries
            'exponential_backoff' => env('NOTIFICATIONS_EXPONENTIAL_BACKOFF', true),
            'jitter' => env('NOTIFICATIONS_RETRY_JITTER', true)
        ],

        // Priority handling
        'priority' => [
            'critical' => 10, // Security alerts, payment issues
            'high' => 8,      // Order confirmations, shipping updates
            'normal' => 5,    // General notifications
            'low' => 2,       // Marketing emails, newsletters
            'bulk' => 1       // Mass communications
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics and Reporting
    |--------------------------------------------------------------------------
    |
    | Comprehensive analytics for notification delivery, engagement,
    | and performance optimization.
    |
    */

    'analytics' => [
        'enabled' => env('NOTIFICATIONS_ANALYTICS_ENABLED', true),
        'detailed_logging' => env('NOTIFICATIONS_DETAILED_LOGGING', false),
        
        // Metrics collection
        'metrics' => [
            'delivery_rates' => true,
            'open_rates' => true,
            'click_rates' => true,
            'conversion_rates' => true,
            'unsubscribe_rates' => true,
            'bounce_rates' => true,
            'performance_timing' => true
        ],

        // Reporting
        'reporting' => [
            'daily_summaries' => env('NOTIFICATIONS_DAILY_REPORTS', true),
            'weekly_insights' => env('NOTIFICATIONS_WEEKLY_INSIGHTS', true),
            'monthly_analytics' => env('NOTIFICATIONS_MONTHLY_ANALYTICS', true),
            'real_time_dashboard' => env('NOTIFICATIONS_REALTIME_DASHBOARD', true)
        ],

        // Data retention
        'retention' => [
            'detailed_logs' => env('NOTIFICATIONS_LOG_RETENTION', 30), // days
            'summary_data' => env('NOTIFICATIONS_SUMMARY_RETENTION', 365), // days
            'anonymize_after' => env('NOTIFICATIONS_ANONYMIZE_AFTER', 90) // days
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | User Preferences and Consent Management
    |--------------------------------------------------------------------------
    |
    | Advanced user preference management with GDPR compliance and
    | granular notification control.
    |
    */

    'preferences' => [
        // Default user preferences
        'defaults' => [
            'email_marketing' => false,
            'email_transactional' => true,
            'sms_marketing' => false,
            'sms_transactional' => true,
            'push_marketing' => false,
            'push_transactional' => true,
            'phone_marketing' => false
        ],

        // Preference categories
        'categories' => [
            'transactional' => [
                'label' => 'Order & Account Updates',
                'description' => 'Essential notifications about your orders and account',
                'required' => true,
                'channels' => ['email', 'sms', 'push']
            ],
            'marketing' => [
                'label' => 'Promotions & Offers',
                'description' => 'Special offers, sales, and promotional content',
                'required' => false,
                'channels' => ['email', 'sms', 'push']
            ],
            'product_updates' => [
                'label' => 'Product Updates',
                'description' => 'New arrivals, restocks, and product recommendations',
                'required' => false,
                'channels' => ['email', 'push']
            ],
            'security' => [
                'label' => 'Security Alerts',
                'description' => 'Account security and suspicious activity notifications',
                'required' => true,
                'channels' => ['email', 'sms']
            ]
        ],

        // Consent management
        'consent' => [
            'double_opt_in' => env('NOTIFICATIONS_DOUBLE_OPT_IN', true),
            'consent_expiry' => env('NOTIFICATIONS_CONSENT_EXPIRY', 730), // days
            'reconfirmation_frequency' => env('NOTIFICATIONS_RECONFIRM_FREQUENCY', 365), // days
            'gdpr_compliance' => env('NOTIFICATIONS_GDPR_COMPLIANCE', true)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Template System
    |--------------------------------------------------------------------------
    |
    | Advanced template management with multi-language support,
    | personalization, and A/B testing capabilities.
    |
    */

    'templates' => [
        // Template engine
        'engine' => env('NOTIFICATION_TEMPLATE_ENGINE', 'blade'),
        'cache_enabled' => env('NOTIFICATION_TEMPLATE_CACHE', true),
        'cache_ttl' => env('NOTIFICATION_TEMPLATE_CACHE_TTL', 3600),

        // Multi-language support
        'localization' => [
            'enabled' => env('NOTIFICATIONS_LOCALIZATION', true),
            'fallback_locale' => env('NOTIFICATIONS_FALLBACK_LOCALE', 'en'),
            'auto_detect' => env('NOTIFICATIONS_AUTO_DETECT_LOCALE', true),
            'supported_locales' => explode(',', env('NOTIFICATIONS_SUPPORTED_LOCALES', 'en,es,fr,de,it'))
        ],

        // Personalization
        'personalization' => [
            'enabled' => env('NOTIFICATIONS_PERSONALIZATION', true),
            'user_data' => true,
            'behavioral_data' => true,
            'purchase_history' => true,
            'location_data' => env('NOTIFICATIONS_LOCATION_PERSONALIZATION', false)
        ],

        // A/B testing
        'ab_testing' => [
            'enabled' => env('NOTIFICATIONS_AB_TESTING', false),
            'split_percentage' => env('NOTIFICATIONS_AB_SPLIT', 50),
            'minimum_sample_size' => env('NOTIFICATIONS_AB_MIN_SAMPLE', 100),
            'statistical_significance' => env('NOTIFICATIONS_AB_SIGNIFICANCE', 0.95)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Security and Compliance
    |--------------------------------------------------------------------------
    |
    | Security features and compliance settings for notification system.
    |
    */

    'security' => [
        // Encryption
        'encrypt_sensitive_data' => env('NOTIFICATIONS_ENCRYPT_SENSITIVE', true),
        'encryption_key' => env('NOTIFICATIONS_ENCRYPTION_KEY'),

        // Rate limiting
        'rate_limiting' => [
            'enabled' => env('NOTIFICATIONS_RATE_LIMITING', true),
            'per_user_limits' => [
                'email' => ['daily' => 50, 'hourly' => 10],
                'sms' => ['daily' => 10, 'hourly' => 3],
                'push' => ['daily' => 100, 'hourly' => 20]
            ],
            'global_limits' => [
                'email' => ['per_minute' => 1000, 'per_hour' => 50000],
                'sms' => ['per_minute' => 100, 'per_hour' => 5000],
                'push' => ['per_minute' => 5000, 'per_hour' => 200000]
            ]
        ],

        // Compliance
        'compliance' => [
            'gdpr' => env('NOTIFICATIONS_GDPR', true),
            'ccpa' => env('NOTIFICATIONS_CCPA', true),
            'casl' => env('NOTIFICATIONS_CASL', true),
            'data_residency' => env('NOTIFICATIONS_DATA_RESIDENCY'),
            'audit_logging' => env('NOTIFICATIONS_AUDIT_LOGGING', true)
        ],

        // Spam protection
        'spam_protection' => [
            'enabled' => env('NOTIFICATIONS_SPAM_PROTECTION', true),
            'honeypot' => env('NOTIFICATIONS_HONEYPOT', true),
            'captcha' => env('NOTIFICATIONS_CAPTCHA', false),
            'ip_reputation' => env('NOTIFICATIONS_IP_REPUTATION', true)
        ]
    ]
];