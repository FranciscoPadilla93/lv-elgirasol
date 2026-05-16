<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'label' => 'Control de usuarios',
                'icon' => 'users',
                'order' => 10,
                'children' => [
                    [
                        'module' => 'users',
                        'label' => 'Usuarios',
                        'path' => '/usuarios',
                        'icon' => 'user',
                        'order' => 10,
                    ],
                    [
                        'module' => 'roles',
                        'label' => 'Roles',
                        'path' => '/roles',
                        'icon' => 'shield',
                        'order' => 20,
                    ],
                    [
                        'module' => 'modules',
                        'label' => 'Módulos',
                        'path' => '/modulos',
                        'icon' => 'box',
                        'order' => 30,
                    ],
                    [
                        'module' => 'permissions',
                        'label' => 'Permisos',
                        'path' => '/permisos',
                        'icon' => 'key',
                        'order' => 40,
                    ],
                    [
                        'module' => 'role_permissions',
                        'label' => 'Asignar permisos',
                        'path' => '/asignar-permisos',
                        'icon' => 'lock-keyhole',
                        'order' => 50,
                    ],
                    [
                        'module' => 'menus',
                        'label' => 'Menús',
                        'path' => '/menus',
                        'icon' => 'menu',
                        'order' => 60,
                    ],
                ],
            ],

            [
                'label' => 'Control escolar',
                'icon' => 'school',
                'order' => 20,
                'children' => [
                    [
                        'module' => 'expedientes',
                        'label' => 'Expedientes',
                        'path' => '/expedientes',
                        'icon' => 'folder-open',
                        'order' => 10,
                    ],
                    [
                        'module' => 'tutores',
                        'label' => 'Tutores',
                        'path' => '/tutores',
                        'icon' => 'users',
                        'order' => 20,
                    ],
                    [
                        'module' => 'expediente_tutores',
                        'label' => 'Expediente tutores',
                        'path' => '/expediente-tutores',
                        'icon' => 'user-check',
                        'order' => 30,
                    ],
                    [
                        'module' => 'expediente_contactos',
                        'label' => 'Contactos',
                        'path' => '/expediente-contactos',
                        'icon' => 'phone',
                        'order' => 40,
                    ],
                    [
                        'module' => 'expediente_documentos',
                        'label' => 'Documentos',
                        'path' => '/expediente-documentos',
                        'icon' => 'file-text',
                        'order' => 50,
                    ],
                    [
                        'module' => 'inscripciones',
                        'label' => 'Inscripciones',
                        'path' => '/inscripciones',
                        'icon' => 'clipboard-list',
                        'order' => 60,
                    ],
                    // [
                    //     'module' => 'ciclos_escolares',
                    //     'label' => 'Ciclos escolares',
                    //     'path' => '/ciclos-escolares',
                    //     'icon' => 'calendar',
                    //     'order' => 70,
                    // ],
                ],
            ],

            [
                'label' => 'Control académico',
                'icon' => 'book-open',
                'order' => 30,
                'children' => [
                    [
                        'module' => 'evaluaciones_iniciales',
                        'label' => 'Evaluaciones iniciales',
                        'path' => '/evaluaciones-iniciales',
                        'icon' => 'clipboard-check',
                        'order' => 10,
                    ],
                ],
            ],

            [
                'label' => 'Servicios de apoyo',
                'icon' => 'heart-handshake',
                'order' => 40,
                'children' => [
                    [
                        'module' => 'estudios_socioeconomicos',
                        'label' => 'Estudios socioeconómicos',
                        'path' => '/estudios-socioeconomicos',
                        'icon' => 'search-check',
                        'order' => 10,
                    ],
                ],
            ],

            [
                'label' => 'Tesorería',
                'icon' => 'circle-dollar-sign',
                'order' => 50,
                'children' => [
                    [
                        'module' => 'conceptos',
                        'label' => 'Conceptos',
                        'path' => '/conceptos',
                        'icon' => 'receipt-text',
                        'order' => 10,
                    ],
                    [
                        'module' => 'conceptos_ciclos_escolares',
                        'label' => 'Conceptos por ciclo escolar',
                        'path' => '/conceptos-ciclos-escolares',
                        'icon' => 'calendar-days',
                        'order' => 20,
                    ],
                ],
            ],

            [
                'label' => 'Recursos Humanos',
                'icon' => 'briefcase-business',
                'order' => 60,
                'children' => [
                    [
                        'module' => 'intranet_users',
                        'label' => 'Usuarios intranet',
                        'path' => '/intranet-users',
                        'icon' => 'id-card',
                        'order' => 10,
                    ],
                ],
            ],

            [
                'label' => 'Administración',
                'icon' => 'settings',
                'order' => 70,
                'children' => [
                    [
                        'module' => 'ciclos_escolares',
                        'label' => 'Ciclos escolares',
                        'path' => '/admin/ciclos-escolares',
                        'icon' => 'calendar',
                        'order' => 10,
                    ],
                ],
            ],
        ];

        $modules = Module::query()
            ->get()
            ->keyBy('code');

        foreach ($menus as $menuData) {
            // MENÚ PADRE
            $parent = Menu::updateOrCreate(
                [
                    'parent_id' => null,
                    'label' => $menuData['label'],
                ],
                [
                    'module_id' => null,
                    'path' => null,
                    'icon' => $menuData['icon'],
                    'order' => $menuData['order'],
                    'status' => 'active',
                ]
            );

            // MENÚS HIJOS
            foreach ($menuData['children'] as $childData) {
                $module = $modules->get($childData['module']);

                if (! $module) {
                    continue;
                }

                Menu::updateOrCreate(
                    [
                        'parent_id' => $parent->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'label' => $childData['label'],
                        'path' => $childData['path'],
                        'icon' => $childData['icon'],
                        'order' => $childData['order'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
