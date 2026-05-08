<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'users' => 'Users',
            'roles' => 'Roles',
            'modules' => 'Modules',
            'permissions' => 'Permissions',
            'expedientes' => 'Expedientes',
        ];

        foreach ($modules as $code => $name) {
            $module = Module::withTrashed()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'status' => 'active',
                ],
            );

            $module->restore();
        }
    }
}
