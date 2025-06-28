<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch exceptions
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // If it's a logout request with expired token, redirect to login with message
            if ($request->is('logout') || $request->is('*/logout')) {
                return redirect('/login')->with('message', 'Your session has expired. You have been logged out.');
            }

            // For other CSRF errors, redirect to login
            return redirect('/login')->with('message', 'Your session has expired. Please log in again.');
        });
    })->create();
