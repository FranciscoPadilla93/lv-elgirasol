<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $exists = User::query()
                ->where('email', $data['email'])
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'email' => ['El correo ya está registrado.'],
                ]);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
                'status' => $data['status'],
                'email_verified_at' => now(),
            ]);

            return $user->load('role');
        });

    }

    public function update(User $user, array $data): User
    {
        $exists = User::query()
                ->where('email', $data['email'])
                ->where('id', '!=', $user->id)
                ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'email' => ['El correo ya está registrado.'],
            ]);
        }

        $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'status' => $data['status'],
            ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => $data['password'],
            ]);
        }

        return $user->load('role');
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->tokens()->delete();

            $user->sessions()
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                    'logout_reason' => 'deleted_user',
                ]);

            $user->delete();
        });
    }

    public function restore(int $id): ?User
    {
        return DB::transaction(function () use ($id) {
            $user = User::withTrashed()->find($id);

            if (!$user || !$user->trashed()) {
                return null;
            }

            $user->restore();

            return $user->load('role');
        });
    }
}
