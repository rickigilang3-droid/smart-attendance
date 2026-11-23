<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Daftarkan alias Role Middleware di sini
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    $directories = [
        $storagePath.'/app/public',
        $storagePath.'/framework/cache/data',
        $storagePath.'/framework/sessions',
        $storagePath.'/framework/testing',
        $storagePath.'/framework/views',
        $storagePath.'/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($directories as $directory) {
        if (! is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }
    }

    $app->useStoragePath($storagePath);

    foreach ([
        'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
        'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
        'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
        'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes-v7.php',
        'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
    ] as $key => $value) {
        if (! isset($_ENV[$key]) && ! isset($_SERVER[$key]) && getenv($key) === false) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv($key.'='.$value);
        }
    }
}

return $app;
