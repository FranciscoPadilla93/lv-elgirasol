<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usersByRole = [
            'super_admin' => [
                'name' => 'Super',
                'apellido_paterno' => 'Administrador',
                'apellido_materno' => 'Padilla',
                'email' => 'super.admin@example.com',
                'puesto' => 'Admin del Sistema',
                'cedula_profesional' => '0123456789',
            ],
            'direccion_general' => [
                'name' => 'Test Dirección General',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'direccion_general@example.com',
                'puesto' => 'Dirección General',
                'cedula_profesional' => '1123456789',
            ],
            'tesoreria' => [
                'name' => 'Test Tesorero',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'tesoreria@example.com',
                'puesto' => 'Tesorero',
                'cedula_profesional' => '2123456789',
            ],
            'recursos_humanos' => [
                'name' => 'Test RH',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'recursos_humanos@example.com',
                'puesto' => 'Capital Humano',
                'cedula_profesional' => '3133456789',
            ],
            'control_escolar' => [
                'name' => 'Test Control Escolar',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'control_escolar@example.com',
                'puesto' => 'Control Escolar',
                'cedula_profesional' => '4123456889',
            ],
            'psicologia' => [
                'name' => 'Test Psicología',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'psicologia@example.com',
                'puesto' => 'Psicología',
                'cedula_profesional' => '5123457889',
            ],
            'pedagogia' => [
                'name' => 'Test Pedagogía',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'pedagogia@example.com',
                'puesto' => 'Pedagogía',
                'cedula_profesional' => '6123456889',
            ],
            'desarrollo_institucional' => [
                'name' => 'Test Desarrollo Institucional',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'desarrollo_institucional@example.com',
                'puesto' => 'Desarrollo Institucional',
                'cedula_profesional' => '7123456889',
            ],
            'direccion_academica_primaria' => [
                'name' => 'Test Direccion Académica Primaria',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'direccion_academica_primaria@example.com',
                'puesto' => 'Direccion Académica Primaria',
                'cedula_profesional' => '8123456889',
            ],
            'direccion_academica_preescolar' => [
                'name' => 'Test Direccion Académica Preescolar',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'direccion_academica_preescolar@example.com',
                'puesto' => 'Direccion Académica Preescolar',
                'cedula_profesional' => '9123456889',
            ],
            'admin' => [
                'name' => 'Test Administración',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'admin@example.com',
                'puesto' => 'Administración',
                'cedula_profesional' => '7223456889',
            ],
            'almacen' => [
                'name' => 'Test Almacén',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'almacen@example.com',
                'puesto' => 'Almacén',
                'cedula_profesional' => '7323456889',
            ],
            'tutor' => [
                'name' => 'Test Tutor',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'tutor@example.com',
                'puesto' => 'Tutor',
                'cedula_profesional' => '7423456889',
            ],
            'coordinacion_psicopedagogia' => [
                'name' => 'Test Coordinacion Psicopedagogía',
                'apellido_paterno' => 'Test',
                'apellido_materno' => '',
                'email' => 'coordinacion_psicopedagogia@example.com',
                'puesto' => 'Coordinacion Psicopedagogía',
                'cedula_profesional' => '7523456889',
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
                    'apellido_paterno' => $userData['apellido_paterno'],
                    'apellido_materno' => $userData['apellido_materno'],
                    'puesto' => $userData['puesto'],
                    'cedula_profesional' => $userData['cedula_profesional'],
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                    'status' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
