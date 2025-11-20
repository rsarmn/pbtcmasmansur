<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\Authenticate::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Routing\Middleware\CanBePerformed::class,
        'guest' => \App\Http\Middleware\Guest::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // ensure admin alias exists (keep existing admin.auth)
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        'admin' => \App\Http\Middleware\AdminAuth::class,
    ];
}