<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Administrador',
            'direccion_general' => 'Dirección General',
            'tesoreria' => 'Tesorería',
            'recursos_humanos' => 'Recursos Humanos',
            'control_escolar' => 'Control Escolar',
            'psicologia' => 'Psicología',
            'pedagogia' => 'Pedagogía',
            'desarrollo_institucional' => 'Desarrollo Institucional',
            'direccion_academica_primaria' => 'Direccion Académica Primaria',
            'direccion_academica_preescolar' => 'Direccion Académica Preescolar',
            'admin' => 'Administración',
            'almacen' => 'Almacén',
            'tutor' => 'Tutor',
            'coordinacion_psicopedagogia' => 'Coordinacion Psicopedagogía',
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
