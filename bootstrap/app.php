<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureUserIsDeveloper;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\UseAccessTokenCookie;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->api(prepend: [
            UseAccessTokenCookie::class,
        ]);

        $middleware->alias([
            'access.token.cookie' => UseAccessTokenCookie::class,
            'active.user' => EnsureUserIsActive::class,
            'developer.only' => EnsureUserIsDeveloper::class,
            'permission' => CheckPermission::class,
        ]);

        /*

         Laravel 9 / 10 (Referencia)

        En versiones anteriores esto se registraba en:
         app/Http/Kernel.php

         protected $routeMiddleware = [
             'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
             'permission'  => \App\Http\Middleware\CheckPermission::class,
         ];

        */
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            return \App\Helpers\ResponseHelper::error(
                'Datos inválidos',
                422,
                $e->errors()
            );
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            $token = $request->bearerToken();

            if (!$token) {
                return \App\Helpers\ResponseHelper::error('Token no proporcionado', 401);
            }

            return \App\Helpers\ResponseHelper::error('Token inválido o expirado', 401);
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            return \App\Helpers\ResponseHelper::error('Recurso no encontrado', 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return \App\Helpers\ResponseHelper::error('No tienes permiso para acceder', 403);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if (config('app.debug')) {
                return \App\Helpers\ResponseHelper::error($e->getMessage(), 500);
            }

            return \App\Helpers\ResponseHelper::error('Error interno del servidor', 500);
        });

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return \App\Helpers\ResponseHelper::error(
                'Demasiados intentos. Intenta nuevamente en unos minutos.',
                429
            );
        });
    })->create();
