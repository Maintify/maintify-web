<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SessionTimeoutMiddleware;
use App\Http\Middleware\WorkshopApprovedMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SessionTimeoutMiddleware::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'workshop.approved' => WorkshopApprovedMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 429);
            }

            return response($e->getMessage(), 429);
        });
    })->create();
