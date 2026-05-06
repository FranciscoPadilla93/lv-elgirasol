<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            'ventas' => [
                'label' => 'Ventas',
                'path' => '/ventas',
                'icon' => 'shopping-cart',
                'order' => 10,
            ],
            'inventario' => [
                'label' => 'Inventario',
                'path' => '/inventario',
                'icon' => 'package',
                'order' => 20,
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
