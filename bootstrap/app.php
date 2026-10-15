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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        // B3: guest — auth sudah login redirect ke dashboard per role (anti loop /lupa-password)
        $middleware->redirectGuestsTo(fn() => route('login'));
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $role = auth()->user()?->role;
            return $role ? route("dashboard.{$role}") : route('login');
        });
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
