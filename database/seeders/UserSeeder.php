<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersByRole = [
            'super_admin' => [
                'name' => 'Super Admin',
                'email' => 'super.admin@example.com',
            ],
            'developer' => [
                'name' => 'Developer',
                'email' => 'developer@example.com',
            ],
            'admin' => [
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ],
            'user' => [
                'name' => 'User',
                'email' => 'user@example.com',
            ],
        ];

        $roles = Role::whereIn('code', array_keys($usersByRole))->get()->keyBy('code');

        foreach ($usersByRole as $roleCode => $userData) {
            $role = $roles->get($roleCode);

            if (! $role) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
