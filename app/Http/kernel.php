<?php

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $middlewareGroups = [
        'api' => [
            \Fruitcake\Cors\HandleCors::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}
