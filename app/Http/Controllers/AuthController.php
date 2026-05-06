<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\MenuResource;
use App\Http\Resources\UsuarioResource;
use App\Models\Menu;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseHelper::error('Credenciales inválidas', 401);
        }

        if ($user->status !== 'active') {
            return ResponseHelper::error('El usuario está inactivo', 403);
        }

        $role = $user->getCachedRoleWithPermissions();

        if ($role !== null) {
            $user->setRelation('role', $role);
        }

        $expiresAt = $this->tokenExpiresAt();

        $token = DB::transaction(function () use ($expiresAt, $request, $user): string {
            UserSession::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                    'logout_reason' => 'new_login',
                ]);

            $user->tokens()->delete();

            $newAccessToken = $user->createToken('api-token', ['*'], $expiresAt);

            UserSession::create([
                'user_id' => $user->id,
                'personal_access_token_id' => $newAccessToken->accessToken->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_in_at' => now(),
                'is_active' => true,
            ]);

            return $newAccessToken->plainTextToken;
        });


        return ResponseHelper::success(
            [
                'usuario' => new UsuarioResource($user),
                'menu' => MenuResource::collection($this->menuForRole($role)),
                'expires_at' => $expiresAt?->toISOString(),
            ],
            'Inicio de sesión exitoso'
        )->cookie(
            'access_token',
            $token,
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
