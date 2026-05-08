<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'developer' => 'Developer',
            'admin' => 'Admin',
            'control_escolar' => 'Control Escolar',
            'user' => 'User',
        ];

        foreach ($roles as $code => $name) {
            $role = Role::withTrashed()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'status' => 'active',
                ],
            );

            $role->restore();
        }
    }
}
