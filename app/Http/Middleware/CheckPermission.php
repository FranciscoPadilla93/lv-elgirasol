<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle($request, Closure $next, string $module, string $permission)
    {
        $user = $request->user();

        // Usuario no autenticado
        if (!$user) {
            return ResponseHelper::error('No autenticado', 401);
        }

        // Validar parámetros
        if (!isset($module, $permission)) {
            return \App\Helpers\ResponseHelper::error('Permiso mal definido en ruta', 500);
        }

        // Normalizar valores
        $module = trim(strtolower($module));
        $permission = trim(strtolower($permission));

        // Validar permiso
        if (!$user->hasPermission($module, $permission)) {
            return \App\Helpers\ResponseHelper::error(
                'No tienes permiso para realizar esta acción',
                403
            );
        }

        return $next($request);
    }
}
