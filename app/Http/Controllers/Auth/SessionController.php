<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->load([
            'role.rolePermissions.module',
            'role.rolePermissions.permission',
        ]);

        return ResponseHelper::success(
            new UsuarioResource($user),
            'Usuario autenticado'
        );
    }
}
