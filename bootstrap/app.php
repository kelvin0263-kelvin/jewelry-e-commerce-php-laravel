<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Aliases from both branches
        $middleware->alias([
            'is_admin'            => \App\Http\Middleware\IsAdmin::class,
            'admin'               => \App\Http\Middleware\IsAdmin::class,
            'rate_limit'          => \App\Http\Middleware\RateLimitMiddleware::class,
            'secure_error_handling'=> \App\Http\Middleware\SecureErrorHandling::class,
            'database_security'   => \App\Http\Middleware\DatabaseSecurity::class,
            // From other branch
            'throttle'            => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

        // Sanctum SPA support (keep both branches' behavior)
        if (method_exists($middleware, 'statefulApi')) {
            $middleware->statefulApi();
        } else {
            // Fallback for older framework: append Sanctum middleware to API group
            $middleware->appendToGroup('api', [
                \Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
