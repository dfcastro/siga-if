<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckIsAdmin; // Importamos a sua classe

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // CÓDIGO CORRETO COMBINADO
        $middleware->alias([
            // A sua regra existente, que vamos manter
            'role' => \App\Http\Middleware\HandleRoleMiddleware::class,

            
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
