<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials, Request $request, ?Carbon $expiresAt = null
    ): array {
        return DB::transaction(function () use (
            $credentials,
            $request,
            $expiresAt
        ) {

            $user = User::query()
                ->where('email', $credentials['email'])
                ->first();

            if (
                !$user ||
                !Hash::check(
                    $credentials['password'],
                    $user->password
                )
            ) {

                throw ValidationException::withMessages([
                    'email' => ['Credenciales inválidas.'],
                ]);
            }

            if ($user->status !== 'active') {

                throw ValidationException::withMessages([
                    'email' => ['El usuario está inactivo.'],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | INVALIDAR SESIONES
            |--------------------------------------------------------------------------
            */
            UserSession::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                    'logout_reason' => 'new_login',
                ]);

            $user->tokens()->delete();

            /*
            |--------------------------------------------------------------------------
            | CACHE ROLE
            |--------------------------------------------------------------------------
            */
            $role = $user->getCachedRoleWithPermissions();

            if ($role !== null) {

                $user->setRelation('role', $role);
            }

            /*
            |--------------------------------------------------------------------------
            | TOKEN
            |--------------------------------------------------------------------------
            */
            $newAccessToken = $user->createToken(
                'api-token',
                ['*'],
                $expiresAt
            );

            /*
            |--------------------------------------------------------------------------
            | SESSION
            |--------------------------------------------------------------------------
            */
            UserSession::create([
                'user_id' => $user->id,
                'personal_access_token_id' => $newAccessToken->accessToken->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_in_at' => now(),
                'is_active' => true,
            ]);

            return [
                'user' => $user,
                'token' => $newAccessToken->plainTextToken,
            ];
        });
    }
}
