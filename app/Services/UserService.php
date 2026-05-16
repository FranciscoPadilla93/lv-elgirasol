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
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'],
                'email' => $data['email'],
                'puesto' => $data['puesto'],
                'cedula_profesional' => $data['cedula_profesional'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
                'email_verified_at' => now(),
            ]);

            $user->refresh();
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
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'],
                'email' => $data['email'],
                'puesto' => $data['puesto'],
                'cedula_profesional' => $data['cedula_profesional'],
                'role_id' => $data['role_id'],
                'status' => $data['status'],
            ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => $data['password'],
            ]);
        }

        $user->refresh();
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

            $user->update([
                'status' => false
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

            $user->update([
                'status' => true
            ]);
            return $user->load('role');
        });
    }
}
