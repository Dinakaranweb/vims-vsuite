<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\AuthenticateHOD;
use App\Http\Middleware\AuthenticateStaff;
use App\Http\Middleware\AuthenticatePostal;
use App\Http\Middleware\AuthenticateITAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        //$app->register(\Barryvdh\DomPDF\ServiceProvider::class);

        $middleware->alias([
            'hod'      => \App\Http\Middleware\AuthenticateHOD::class,
            'staff'    => \App\Http\Middleware\AuthenticateStaff::class,
            'admin'    => \App\Http\Middleware\AuthenticateAdmin::class,
            'itadmin'  => AuthenticateITAdmin::class,
            'postal'   => \App\Http\Middleware\AuthenticatePostal::class,
            'PDF'      => Barryvdh\DomPDF\Facade::class,
            'api.auth' => \App\Http\Middleware\ApiAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
