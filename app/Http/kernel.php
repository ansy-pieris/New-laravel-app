<?php

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $middlewareGroups = [
        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Fruitcake\Cors\HandleCors::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}
