<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('login.index'));
        $middleware->web(App\Http\Middleware\IPAccessMiddleware::class);

        $middleware->alias([
            'IsEmptyUserInformation' => \App\Http\Middleware\IsEmptyUserInformation::class,
            'LimitedPurchase' => \App\Http\Middleware\LimitedPurchaseMiddleware::class,
            'AdminLogin' => \App\Http\Middleware\AdminMiddleware::class,
            'guest'=>\App\Http\Middleware\GuestMiddleware::class,
            'IPAccess'=>\App\Http\Middleware\IPAccessMiddleware::class
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
