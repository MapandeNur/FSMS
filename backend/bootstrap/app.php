<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

<<<<<<< HEAD
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
=======
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
>>>>>>> a45ebbfdcd09c358913bc2cf7dd54bcbf2254134
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
<<<<<<< HEAD
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
=======
        //
>>>>>>> a45ebbfdcd09c358913bc2cf7dd54bcbf2254134
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
<<<<<<< HEAD
    ->create();
=======
    ->create();

return $app;
>>>>>>> a45ebbfdcd09c358913bc2cf7dd54bcbf2254134
