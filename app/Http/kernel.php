<?php

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $middlewareGroups = [
        'api' => [
            // Removed EnsureFrontendRequestsAreStateful for true API usage
            \Fruitcake\Cors\HandleCors::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}
