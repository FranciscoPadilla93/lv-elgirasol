<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()?->status !== 'active') {
            return response()->json([
                'status' => false,
                'status_code' => 403,
                'message' => 'El usuario esta inactivo.',
            ], 403);
        }

        return $next($request);
    }
}
