<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Advanced Sanctum Token Middleware for Outstanding API Security
 * 
 * This middleware provides exceptional security features including:
 * - Token scope validation
 * - Multi-device management
 * - Activity monitoring
 * - Rate limiting
 * - Security analytics
 * - Geolocation validation
 * - Token rotation and refresh
 */
class EnhancedSanctumAuth
{
    /**
     * Handle an incoming request with advanced authentication features
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $scope
     * @param string|null $deviceType
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $scope = null, ?string $deviceType = null)
    {
        try {
            // Extract and validate token
            $token = $this->extractToken($request);
            if (!$token) {
                return $this->unauthorizedResponse('Token not provided');
            }

            // Get personal access token
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return $this->unauthorizedResponse('Invalid token');
            }

            // Check token expiration with advanced strategies
            if ($this->isTokenExpired($accessToken, $request)) {
                return $this->unauthorizedResponse('Token expired');
            }

            // Validate token scopes
            if ($scope && !$this->hasValidScope($accessToken, $scope)) {
                return $this->forbiddenResponse('Insufficient token scope');
            }

            // Device type validation
            if ($deviceType && !$this->isValidDeviceType($accessToken, $deviceType, $request)) {
                return $this->forbiddenResponse('Invalid device type for token');
            }

            // Security validations
            if (!$this->passesSecurityChecks($accessToken, $request)) {
                return $this->unauthorizedResponse('Security validation failed');
            }

            // Rate limiting with role-based limits
            if (!$this->checkRateLimit($accessToken, $request)) {
                return $this->rateLimitResponse('Rate limit exceeded');
            }

            // Activity monitoring and analytics
            $this->trackTokenUsage($accessToken, $request);

            // Update token activity
            $this->updateTokenActivity($accessToken, $request);

            // Token rotation if enabled
            $rotatedToken = $this->handleTokenRotation($accessToken, $request);

            // Set authenticated user
            $request->setUserResolver(function () use ($accessToken) {
                return $accessToken->tokenable;
            });

            // Add security headers
            $response = $next($request);
            $response = $this->addSecurityHeaders($response, $accessToken);

            // Add rotated token to response if applicable
            if ($rotatedToken) {
                $response->header('X-New-Token', $rotatedToken);
                $response->header('X-Token-Rotation', 'true');
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Enhanced Sanctum Auth Error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url()
            ]);

            return $this->unauthorizedResponse('Authentication error');
        }
    }

    /**
     * Extract token from request headers
     */
    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        // Alternative methods
        return $request->input('token') ?? $request->query('api_token');
    }

    /**
     * Check if token is expired using advanced expiration strategies
     */
    private function isTokenExpired(PersonalAccessToken $token, Request $request): bool
    {
        $config = Config::get('sanctum.expiration_strategy');
        
        // No expiration set
        if (!$token->expires_at) {
            return false;
        }

        $now = Carbon::now();
        $expiresAt = Carbon::parse($token->expires_at);
        
        // Absolute expiration
        if ($config['absolute_expiration'] && $now->greaterThan($expiresAt)) {
            return true;
        }

        // Sliding expiration - extend token on use
        if ($config['sliding_expiration']) {
            $lastUsed = Carbon::parse($token->last_used_at ?? $token->created_at);
            $slidingWindow = $config['sliding_window']; // minutes
            
            if ($now->diffInMinutes($lastUsed) > $slidingWindow) {
                // Extend token expiration
                $token->expires_at = $now->addMinutes($slidingWindow);
                $token->save();
            }
        }

        // Inactivity timeout
        if ($config['inactivity_timeout']) {
            $lastUsed = Carbon::parse($token->last_used_at ?? $token->created_at);
            $inactivityLimit = $config['inactivity_timeout']; // minutes
            
            if ($now->diffInMinutes($lastUsed) > $inactivityLimit) {
                return true;
            }
        }

        return $now->greaterThan($expiresAt);
    }

    /**
     * Validate token has required scope
     */
    private function hasValidScope(PersonalAccessToken $token, string $requiredScope): bool
    {
        $tokenScopes = $token->abilities ?? [];
        
        // Wildcard access
        if (in_array('*', $tokenScopes)) {
            return true;
        }

        // Direct scope match
        if (in_array($requiredScope, $tokenScopes)) {
            return true;
        }

        // Check for parent scopes (e.g., 'user:admin' includes 'user:read')
        $scopeParts = explode(':', $requiredScope);
        if (count($scopeParts) === 2) {
            $parentScope = $scopeParts[0] . ':admin';
            if (in_array($parentScope, $tokenScopes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate device type compatibility
     */
    private function isValidDeviceType(PersonalAccessToken $token, string $requiredDeviceType, Request $request): bool
    {
        $tokenDeviceType = $token->getAttribute('device_type') ?? 'web';
        
        // Exact match
        if ($tokenDeviceType === $requiredDeviceType) {
            return true;
        }

        // Check device compatibility matrix
        $compatibilityMatrix = [
            'admin_panel' => ['web'],
            'api_client' => ['web', 'mobile_app', 'desktop_app'],
            'mobile_app' => ['mobile_app'],
            'pos_system' => ['pos_system', 'desktop_app']
        ];

        return in_array($requiredDeviceType, $compatibilityMatrix[$tokenDeviceType] ?? []);
    }

    /**
     * Perform comprehensive security checks
     */
    private function passesSecurityChecks(PersonalAccessToken $token, Request $request): bool
    {
        $config = Config::get('sanctum.security');
        
        // IP whitelist/blacklist validation
        if ($this->isIpBlocked($request->ip(), $config)) {
            $this->logSecurityEvent('ip_blocked', $token, $request);
            return false;
        }

        // Geolocation validation
        if ($config['ip_geofencing']['enabled'] && !$this->isLocationAllowed($request->ip(), $config)) {
            $this->logSecurityEvent('geo_blocked', $token, $request);
            return false;
        }

        // Suspicious activity detection
        if ($config['activity_monitoring']['enabled'] && $this->detectSuspiciousActivity($token, $request)) {
            $this->logSecurityEvent('suspicious_activity', $token, $request);
            return false;
        }

        // Token fingerprinting
        if ($config['token_security']['token_fingerprinting'] && !$this->validateTokenFingerprint($token, $request)) {
            $this->logSecurityEvent('fingerprint_mismatch', $token, $request);
            return false;
        }

        return true;
    }

    /**
     * Check IP address against whitelist/blacklist
     */
    private function isIpBlocked(string $ip, array $config): bool
    {
        // Check blacklist first
        if (!empty($config['ip_blacklist']) && in_array($ip, $config['ip_blacklist'])) {
            return true;
        }

        // Check whitelist (if configured)
        if (!empty($config['ip_whitelist']) && !in_array($ip, $config['ip_whitelist'])) {
            return true;
        }

        return false;
    }

    /**
     * Validate request location against geofencing rules
     */
    private function isLocationAllowed(string $ip, array $config): bool
    {
        $geoConfig = $config['ip_geofencing'];
        
        // Get country from IP (simplified - would use actual geolocation service)
        $country = $this->getCountryFromIp($ip);
        
        // Check blocked countries
        if (!empty($geoConfig['blocked_countries']) && in_array($country, $geoConfig['blocked_countries'])) {
            return false;
        }

        // Check allowed countries
        if (!empty($geoConfig['allowed_countries']) && !in_array($country, $geoConfig['allowed_countries'])) {
            return false;
        }

        return true;
    }

    /**
     * Detect suspicious activity patterns
     */
    private function detectSuspiciousActivity(PersonalAccessToken $token, Request $request): bool
    {
        $cacheKey = "suspicious_activity:token:{$token->id}";
        $activity = Cache::get($cacheKey, []);
        
        // Track request patterns
        $activity[] = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => Carbon::now()->timestamp,
            'endpoint' => $request->path(),
            'method' => $request->method()
        ];

        // Keep only recent activity (last hour)
        $activity = array_filter($activity, function($item) {
            return (Carbon::now()->timestamp - $item['timestamp']) <= 3600;
        });

        // Detect patterns
        $uniqueIps = array_unique(array_column($activity, 'ip'));
        $uniqueUserAgents = array_unique(array_column($activity, 'user_agent'));
        
        // Flag suspicious patterns
        if (count($uniqueIps) > 5 || count($uniqueUserAgents) > 3) {
            Cache::put($cacheKey, $activity, 3600); // Cache for 1 hour
            return true;
        }

        Cache::put($cacheKey, $activity, 3600);
        return false;
    }

    /**
     * Validate token fingerprint
     */
    private function validateTokenFingerprint(PersonalAccessToken $token, Request $request): bool
    {
        $storedFingerprint = $token->getAttribute('fingerprint');
        if (!$storedFingerprint) {
            return true; // No fingerprint stored
        }

        $currentFingerprint = $this->generateFingerprint($request);
        return $storedFingerprint === $currentFingerprint;
    }

    /**
     * Generate device/browser fingerprint
     */
    private function generateFingerprint(Request $request): string
    {
        $components = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->header('Accept'),
        ];

        return hash('sha256', implode('|', array_filter($components)));
    }

    /**
     * Check rate limits with role-based restrictions
     */
    private function checkRateLimit(PersonalAccessToken $token, Request $request): bool
    {
        $config = Config::get('sanctum.security.rate_limits');
        $user = $token->tokenable;
        
        // Get role-specific limits
        $roleConfig = $config['role_based_limits'][$user->role] ?? $config['api_requests'];
        
        $cacheKeys = [
            "rate_limit:token:{$token->id}:minute" => ['limit' => $roleConfig['per_minute'], 'window' => 60],
            "rate_limit:token:{$token->id}:hour" => ['limit' => $roleConfig['per_hour'], 'window' => 3600],
        ];

        foreach ($cacheKeys as $key => $settings) {
            $current = Cache::get($key, 0);
            if ($current >= $settings['limit']) {
                return false;
            }
            
            Cache::put($key, $current + 1, $settings['window']);
        }

        return true;
    }

    /**
     * Track token usage for analytics
     */
    private function trackTokenUsage(PersonalAccessToken $token, Request $request): void
    {
        if (!Config::get('sanctum.analytics.enabled')) {
            return;
        }

        $usageData = [
            'token_id' => $token->id,
            'user_id' => $token->tokenable_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'timestamp' => Carbon::now(),
            'device_type' => $token->getAttribute('device_type'),
            'scopes_used' => $token->abilities
        ];

        // Store in analytics queue/database
        Cache::tags(['sanctum_analytics'])->put(
            "usage:" . Str::uuid(),
            $usageData,
            3600
        );
    }

    /**
     * Update token last used information
     */
    private function updateTokenActivity(PersonalAccessToken $token, Request $request): void
    {
        $token->forceFill([
            'last_used_at' => Carbon::now(),
            'last_used_ip' => $request->ip(),
            'last_used_user_agent' => $request->userAgent()
        ])->save();
    }

    /**
     * Handle token rotation if enabled
     */
    private function handleTokenRotation(PersonalAccessToken $token, Request $request): ?string
    {
        if (!Config::get('sanctum.security.token_security.token_rotation')) {
            return null;
        }

        // Rotate token after certain conditions (time, usage count, etc.)
        $shouldRotate = $this->shouldRotateToken($token);
        
        if ($shouldRotate) {
            $user = $token->tokenable;
            $deviceType = $token->getAttribute('device_type') ?? 'web';
            
            // Create new token with same scopes and device type
            $newToken = $user->createToken(
                'rotated_' . $token->name,
                $token->abilities
            )->plainTextToken;

            // Mark old token for deletion (delayed for graceful transition)
            Cache::put("token_rotation:old:{$token->id}", true, 300); // 5 minutes grace period

            return $newToken;
        }

        return null;
    }

    /**
     * Determine if token should be rotated
     */
    private function shouldRotateToken(PersonalAccessToken $token): bool
    {
        // Rotate tokens that are over 24 hours old
        $age = Carbon::now()->diffInHours(Carbon::parse($token->created_at));
        return $age >= 24;
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders(Response $response, PersonalAccessToken $token): Response
    {
        if (!Config::get('sanctum.security.token_security.secure_headers')) {
            return $response;
        }

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-API-Version' => Config::get('sanctum.integrations.versioning.default_version'),
            'X-Rate-Limit-Remaining' => $this->getRemainingRateLimit($token),
            'X-Token-Expires' => $token->expires_at ? Carbon::parse($token->expires_at)->toISOString() : 'never'
        ];

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }

    /**
     * Get remaining rate limit for token
     */
    private function getRemainingRateLimit(PersonalAccessToken $token): int
    {
        $config = Config::get('sanctum.security.rate_limits');
        $user = $token->tokenable;
        $roleConfig = $config['role_based_limits'][$user->role] ?? $config['api_requests'];
        
        $used = Cache::get("rate_limit:token:{$token->id}:minute", 0);
        return max(0, $roleConfig['per_minute'] - $used);
    }

    /**
     * Log security events
     */
    private function logSecurityEvent(string $event, PersonalAccessToken $token, Request $request): void
    {
        Log::warning("Sanctum Security Event: {$event}", [
            'token_id' => $token->id,
            'user_id' => $token->tokenable_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'timestamp' => Carbon::now()->toISOString()
        ]);

        // Store in security analytics
        Cache::tags(['sanctum_security'])->put(
            "security_event:" . Str::uuid(),
            [
                'event' => $event,
                'token_id' => $token->id,
                'details' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->url()
                ],
                'timestamp' => Carbon::now()
            ],
            86400 // 24 hours
        );
    }

    /**
     * Get country from IP address (simplified implementation)
     */
    private function getCountryFromIp(string $ip): string
    {
        // In production, integrate with actual geolocation service
        // This is a simplified implementation
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'US'; // Localhost
        }
        
        return 'US'; // Default for demo
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
            'code' => 'AUTH_FAILED'
        ], 401);
    }

    /**
     * Return forbidden response
     */
    private function forbiddenResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Forbidden',
            'message' => $message,
            'code' => 'ACCESS_DENIED'
        ], 403);
    }

    /**
     * Return rate limit response
     */
    private function rateLimitResponse(string $message): Response
    {
        return response()->json([
            'error' => 'Too Many Requests',
            'message' => $message,
            'code' => 'RATE_LIMIT_EXCEEDED'
        ], 429);
    }
}