<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsByRole = [
            'super_admin' => ['read', 'create', 'update', 'delete', 'export', 'import', 'assign_permissions',],
            'developer' => ['read','create','update','delete','export','import','assign_permissions',],
            'admin' => ['read','create','update','delete','export','import',],
            'control_escolar' => ['read','create','update'],
            'user' => ['read',],
        ];

        $modulesByRole = [
            // ACCESO TOTAL
            'super_admin' => [
                'users',
                'roles',
                'modules',
                'permissions',
                'expedientes',
            ],

            'developer' => [
                'users',
                'roles',
                'modules',
                'permissions',
                'expedientes',
            ],

            'admin' => [
                'users',
                'roles',
                'expedientes',
            ],
            'control_escolar' => [
                'expedientes',
            ],
            'user' => [
                'expedientes',
            ],
        ];

        $roles = Role::query()
            ->whereIn(
                'code',
                array_keys($permissionsByRole)
            )
            ->get()
            ->keyBy('code');

        $permissions = Permission::query()
            ->whereIn(
                'code',
                collect($permissionsByRole)
                    ->flatten()
                    ->unique()
            )
            ->get()
            ->keyBy('code');

        $modules = Module::all()
            ->keyBy('code');

        foreach ($permissionsByRole as $roleCode => $permissionCodes) {
            $role = $roles->get($roleCode);

            if (! $role) {
                continue;
            }

            $roleModules = $modulesByRole[$roleCode] ?? [];

            foreach ($roleModules as $moduleCode) {
                $module = $modules->get($moduleCode);

                if (! $module) {
                    continue;
                }

                foreach ($permissionCodes as $permissionCode) {
                    $permission = $permissions->get($permissionCode);

                    if (! $permission) {
                        continue;
                    }

                    RolePermission::updateOrCreate(
                        [
                            'role_id' => $role->id,
                            'module_id' => $module->id,
                            'permission_id' => $permission->id,
                        ],
                        [
                            'allowed' => true,
                        ],
                    );
                }
            }
        }
    }
}
