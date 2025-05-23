<?php

use App\Exceptions\ValidationException;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\ExceptionHandler;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\CheckUserStatusMiddleware;
use App\Services\Common\Auth\Exceptions\UserIsDeactivatedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api()->use([CorsMiddleware::class])
            ->alias([
                'role'       => RoleMiddleware::class,
                'permission' => PermissionMiddleware::class,
                'check-user-status' => CheckUserStatusMiddleware::class,
            ]);
        if (env('APP_ENV') !== 'local') {
            $middleware->web()->use([\App\Http\Middleware\TrustedProxies::class]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [
                    'common' => [
                        $e->getMessage(),
                    ]
                ]
            ], 422);
        });
        Integration::handles($exceptions);

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            // TODO: треба розібратись яка помилка і повертати json відповідь
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
