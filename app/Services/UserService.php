<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);

            return $user->load('role');
        });

    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return $user->load('role');
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
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
