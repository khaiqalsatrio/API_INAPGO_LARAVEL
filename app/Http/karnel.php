<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class, // ✅ perbaikan namespace
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class, // ✅
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // ✅
            \Illuminate\Session\Middleware\StartSession::class, // ✅
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // ✅
            \App\Http\Middleware\VerifyCsrfToken::class, // ✅ (ini memang biasanya custom)
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
    
    protected $routeMiddleware = [
        'auth.check' => \App\Http\Middleware\AuthCheck::class,
        // 'auth.token' => \App\Http\Middleware\AuthCheck::class, // ✅ Tambahkan ini
    ];
}
