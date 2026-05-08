<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;

use App\Models\Menu;
use App\Models\User;
use App\Models\UserSession;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\MenuResource;
use App\Http\Resources\UsuarioResource;

use App\Services\AuthService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $expiresAt = $this->tokenExpiresAt();

        $result = $this->authService->login(
            $request->validated(),
            $request,
            $expiresAt
        );

        return ResponseHelper::success(
            [
                'usuario' => new UsuarioResource(
                    $result['user']
                ),

                'menu' => MenuResource::collection(
                    $this->menuForRole(
                        $result['user']->role
                    )
                ),

                'expires_at' => $expiresAt?->toISOString(),
            ],
            'Inicio de sesión exitoso'
        )->cookie(
            'access_token',
            $result['token'],
            $this->tokenCookieMinutes(),
            config('session.path', '/'),
            config('session.domain'),
            (bool) config('session.secure', $request->isSecure()),
            true,
            false,
            config('session.same_site', 'lax')
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $accessToken = $request->user()?->currentAccessToken();

        if ($accessToken !== null) {
            UserSession::query()
                ->where('personal_access_token_id', $accessToken->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                    'logout_reason' => 'logout',
                ]);

            $accessToken->delete();
        }

        return ResponseHelper::success(
            null,
            'Sesión cerrada correctamente'
        )->withoutCookie('access_token');
    }

    private function tokenExpiresAt(): ?Carbon
    {
        $expiration = config('sanctum.expiration');

        return $expiration === null ? null : now()->addMinutes((int) $expiration);
    }

    private function tokenCookieMinutes(): int
    {
        return (int) (config('sanctum.expiration') ?? 60 * 24 * 7);
    }

    private function menuForRole($role)
    {
        if ($role === null) {
            return collect();
        }

        $moduleIds = $role->rolePermissions
            ->where('allowed', true)
            ->pluck('module_id')
            ->unique()
            ->values();

        if ($moduleIds->isEmpty()) {
            return collect();
        }

        return Menu::query()
            ->with(['module', 'parent'])
            ->whereIn('module_id', $moduleIds)
            ->where('status', 'active')
            ->orderBy('order')
            ->orderBy('label')
            ->get();
    }
}
