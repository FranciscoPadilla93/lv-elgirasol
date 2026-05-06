<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsByRole = [
            'super_admin' => ['read', 'create', 'update', 'delete', 'export', 'import', 'assign_permissions'],
            'developer' => ['read', 'create', 'update', 'delete', 'export', 'import', 'assign_permissions'],
            'admin' => ['read', 'create', 'update', 'delete', 'export', 'import'],
            'admin_ventas' => ['read', 'create', 'update', 'delete'],
            'admin_inventario' => ['read', 'create', 'update', 'delete'],
            'user' => ['read'],
        ];

        $modulesByRole = [
            'admin_ventas' => ['ventas'],
            'admin_inventario' => ['inventario'],
        ];

        $roles = Role::whereIn('code', array_keys($permissionsByRole))->get()->keyBy('code');
        $modules = Module::whereIn('code', ['users', 'roles', 'modules', 'permissions', 'ventas', 'inventario'])->get();
        $permissions = Permission::whereIn('code', collect($permissionsByRole)->flatten()->unique())->get()->keyBy('code');

        foreach ($permissionsByRole as $roleCode => $permissionCodes) {
            $role = $roles->get($roleCode);

            if (! $role) {
                continue;
            }

            $roleModules = $modulesByRole[$roleCode] ?? ['users', 'roles', 'modules', 'permissions', 'ventas', 'inventario'];

            foreach ($modules->whereIn('code', $roleModules) as $module) {
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
                        ['allowed' => true],
                    );
                }
            }
        }
    }
}
