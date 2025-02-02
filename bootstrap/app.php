<?php

use App\Http\Middleware\EnforceAcceptHeaderIsSet;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global Middleware
        $middleware->append(CheckForMaintenanceMode::class);

        // Middleware Groups
        $middleware->appendToGroup('api', [
            EnforceAcceptHeaderIsSet::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();
