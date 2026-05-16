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
        $this->assignAllPermissionsToRole('super_admin');

        $this->assignPermissions('direccion_general', [
            'users' => ['read'],
            'roles' => ['read'],
            'modules' => ['read'],
            'permissions' => ['read'],
            'role_permissions' => ['read'],
            'menus' => ['read'],
            'catalogs' => ['read'],
            'expedientes' => ['read', 'export'],
            'tutores' => ['read', 'export'],
            'expediente_tutores' => ['read', 'export'],
            'expediente_contactos' => ['read', 'export'],
            'expediente_documentos' => ['read', 'export'],
            'inscripciones' => ['read', 'export'],
            'ciclos_escolares' => ['read'],

            'evaluaciones_iniciales' => ['read', 'export'],
            'estudios_socioeconomicos' => ['read', 'export'],

            'conceptos' => ['read', 'export'],
            'conceptos_ciclos_escolares' => ['read', 'export'],

            'intranet_users' => ['read', 'export'],
        ]);

        $this->assignPermissions('control_escolar', [
            'expedientes' => ['read', 'create', 'update', 'delete', 'export'],
            'tutores' => ['read', 'create', 'update', 'delete', 'export'],
            'expediente_tutores' => ['read', 'create', 'update', 'delete'],
            'expediente_contactos' => ['read', 'create', 'update', 'delete'],
            'expediente_documentos' => ['read', 'create', 'update', 'delete'],
            'inscripciones' => ['read', 'create', 'update', 'delete', 'export'],
            'ciclos_escolares' => ['read'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('tesoreria', [
            'conceptos' => ['read', 'create', 'update', 'delete', 'export'],
            'conceptos_ciclos_escolares' => ['read', 'create', 'update', 'delete', 'export'],

            // Consulta necesaria para revisar alumnos/inscripciones
            'expedientes' => ['read'],
            'inscripciones' => ['read', 'update'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('recursos_humanos', [
            'intranet_users' => ['read', 'create', 'update', 'delete', 'export'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('psicologia', [
            'evaluaciones_iniciales' => ['read', 'create', 'update', 'export'],
            'expedientes' => ['read'],
            'inscripciones' => ['read'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('pedagogia', [
            'evaluaciones_iniciales' => ['read', 'create', 'update', 'export'],
            'expedientes' => ['read'],
            'inscripciones' => ['read'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('coordinacion_psicopedagogia', [
            'evaluaciones_iniciales' => ['read', 'create', 'update', 'export'],
            'estudios_socioeconomicos' => ['read', 'create', 'update', 'export'],
            'expedientes' => ['read'],
            'inscripciones' => ['read'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('desarrollo_institucional', [
            'estudios_socioeconomicos' => ['read', 'create', 'update', 'export'],
            'expedientes' => ['read'],
            'inscripciones' => ['read'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('direccion_academica_primaria', [
            'expedientes' => ['read', 'export'],
            'inscripciones' => ['read', 'update', 'export'],
            'evaluaciones_iniciales' => ['read', 'update', 'export'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('direccion_academica_preescolar', [
            'expedientes' => ['read', 'export'],
            'inscripciones' => ['read', 'update', 'export'],
            'evaluaciones_iniciales' => ['read', 'update', 'export'],
            'catalogs' => ['read'],
        ]);

        $this->assignPermissions('admin', [
            'ciclos_escolares' => ['read', 'create', 'update', 'delete'],
            'conceptos' => ['read'],
            'conceptos_ciclos_escolares' => ['read'],
            'intranet_users' => ['read'],
            'catalogs' => ['read'],
        ]);

        // Por ahora sin permisos administrativos porque no existen módulos para estos roles hasta la fase 2
        $this->assignPermissions('almacen', []);

        $this->assignPermissions('tutor', []);
    }

    private function assignAllPermissionsToRole(string $roleCode): void
    {
        $role = Role::query()
            ->where('code', $roleCode)
            ->first();

        if (! $role) {
            return;
        }

        $modules = Module::query()
            ->where('status', 'active')
            ->get();

        $permissions = Permission::query()
            ->get();

        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'module_id' => $module->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'allowed' => true,
                    ]
                );
            }
        }
    }

    private function assignPermissions(string $roleCode, array $modulePermissions): void
    {
        $role = Role::query()
            ->where('code', $roleCode)
            ->first();

        if (! $role) {
            return;
        }

        foreach ($modulePermissions as $moduleCode => $permissionCodes) {
            $module = Module::query()
                ->where('code', $moduleCode)
                ->first();

            if (! $module) {
                continue;
            }

            foreach ($permissionCodes as $permissionCode) {
                $permission = Permission::query()
                    ->where('code', $permissionCode)
                    ->first();

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
                    ]
                );
            }
        }
    }
}
