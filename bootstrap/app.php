<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register all middleware aliases for outstanding Sanctum integration
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'enhanced.sanctum' => \App\Http\Middleware\Api\EnhancedSanctumAuth::class,
            'throttle.auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,15',
            'throttle.api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
        ]);

        // API-specific middleware groups
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
