<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {

            $exists = Role::query()
                ->where('code', $data['code'])
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' => ['El código ya está registrado.'],
                ]);
            }

            return Role::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {

            $exists = Role::query()
                ->where('code', $data['code'])
                ->where('id', '!=', $role->id)
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' => ['El código ya está registrado.'],
                ]);
            }

            $role->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            return $role;
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role) {
            if ($role->users()->exists()) {

                throw ValidationException::withMessages([
                    'role' => [
                        'No puedes eliminar un rol con usuarios asignados.',
                    ],
                ]);
            }

            $role->delete();
        });
    }

    public function restore(int $id): ?Role
    {
        return DB::transaction(function () use ($id) {

            $role = Role::withTrashed()
                ->find($id);

            if (!$role || !$role->trashed()) {

                return null;
            }

            $role->restore();

            return $role;
        });
    }

    public function assignToUser(User $user, int $roleId): User
    {
        return DB::transaction(function () use ($user, $roleId) {

            $role = Role::active()
                ->findOrFail($roleId);

            $user->update([
                'role_id' => $role->id,
            ]);

            return $user->load('role');
        });
    }
}
