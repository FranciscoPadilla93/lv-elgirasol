<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'developer' => 'Developer',
            'admin' => 'Admin',
            'admin_ventas' => 'Admin Ventas',
            'admin_inventario' => 'Admin Inventario',
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
