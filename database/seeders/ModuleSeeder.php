<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // CONTROL DE USUARIOS
            [
                'code' => 'users',
                'name' => 'Usuarios',
                'description' => 'Gestión de usuarios del sistema',
            ],
            [
                'code' => 'roles',
                'name' => 'Roles',
                'description' => 'Gestión de roles del sistema',
            ],
            [
                'code' => 'modules',
                'name' => 'Módulos',
                'description' => 'Gestión de módulos del sistema',
            ],
            [
                'code' => 'permissions',
                'name' => 'Permisos',
                'description' => 'Gestión de permisos del sistema',
            ],
            [
                'code' => 'role_permissions',
                'name' => 'Permisos por rol',
                'description' => 'Asignación de permisos por rol y módulo',
            ],
            [
                'code' => 'menus',
                'name' => 'Menús',
                'description' => 'Gestión de menús del sistema',
            ],

            // CONTROL ESCOLAR
            [
                'code' => 'expedientes',
                'name' => 'Expedientes',
                'description' => 'Gestión de expedientes de alumnos',
            ],
            [
                'code' => 'tutores',
                'name' => 'Tutores',
                'description' => 'Gestión de tutores',
            ],
            [
                'code' => 'expediente_tutores',
                'name' => 'Expediente tutores',
                'description' => 'Relación entre expedientes y tutores',
            ],
            [
                'code' => 'expediente_contactos',
                'name' => 'Contactos de expediente',
                'description' => 'Gestión de contactos del expediente',
            ],
            [
                'code' => 'expediente_documentos',
                'name' => 'Documentos de expediente',
                'description' => 'Gestión de documentos del expediente',
            ],
            [
                'code' => 'ciclos_escolares',
                'name' => 'Ciclos escolares',
                'description' => 'Gestión de ciclos escolares',
            ],
            [
                'code' => 'inscripciones',
                'name' => 'Inscripciones',
                'description' => 'Gestión de inscripciones',
            ],

            // CONTROL ACADÉMICO
            [
                'code' => 'evaluaciones_iniciales',
                'name' => 'Evaluaciones iniciales',
                'description' => 'Gestión de evaluaciones iniciales',
            ],

            // SERVICIOS DE APOYO
            [
                'code' => 'estudios_socioeconomicos',
                'name' => 'Estudios socioeconómicos',
                'description' => 'Gestión de estudios socioeconómicos',
            ],

            // TESORERÍA
            [
                'code' => 'conceptos',
                'name' => 'Conceptos',
                'description' => 'Gestión de conceptos de cobro',
            ],
            [
                'code' => 'conceptos_ciclos_escolares',
                'name' => 'Conceptos por ciclo escolar',
                'description' => 'Relación de conceptos con ciclos escolares',
            ],

            // RECURSOS HUMANOS / INTRANET
            [
                'code' => 'intranet_users',
                'name' => 'Usuarios intranet',
                'description' => 'Gestión de usuarios de intranet',
            ],

            // CATALOGOS
            [
                'code' => 'catalogs',
                'name' => 'Catálogos',
                'description' => 'Consulta general de catálogos del sistema',
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                [
                    'code' => $module['code'],
                ],
                [
                    'name' => $module['name'],
                    'description' => $module['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
