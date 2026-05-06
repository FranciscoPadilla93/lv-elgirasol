<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDeveloper
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return ResponseHelper::error('No autenticado', 401);
        }

        if (! $user->hasRole('developer')) {
            return ResponseHelper::error('Solo desarrollador puede acceder a este endpoint', 403);
        }

        return $next($request);
    }
}
