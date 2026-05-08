<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ESCOLAR
            'expedientes' => [
                'label' => 'Expedientes',
                'path' => '/expedientes',
                'icon' => 'folder-open',
                'order' => 10,
            ],
        ];

        $modules = Module::whereIn('code', array_keys($items))->get()->keyBy('code');

        foreach ($items as $moduleCode => $item) {
            $module = $modules->get($moduleCode);

            if (! $module) {
                continue;
            }

            Menu::updateOrCreate(
                [
                    'module_id' => $module->id,
                    'parent_id' => null,
                    'path' => $item['path'],
                ],
                [
                    'label' => $item['label'],
                    'icon' => $item['icon'],
                    'order' => $item['order'],
                    'status' => 'active',
                ],
            );
        }
    }
}
