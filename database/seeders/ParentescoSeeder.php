<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ParentescoSeeder extends Seeder
{
    public function run(): void
    {
        $parentescos = [
            [
                'code' => 'mother',
                'name' => 'Madre',
                'status' => true,
            ],
            [
                'code' => 'father',
                'name' => 'Padre',
                'status' => true,
            ],
            [
                'code' => 'guardian',
                'name' => 'Tutor',
                'status' => true,
            ],
            [
                'code' => 'grandmother',
                'name' => 'Abuela',
                'status' => true,
            ],
            [
                'code' => 'grandfather',
                'name' => 'Abuelo',
                'status' => true,
            ],
            [
                'code' => 'uncle',
                'name' => 'Tío',
                'status' => true,
            ],
            [
                'code' => 'aunt',
                'name' => 'Tía',
                'status' => true,
            ],
            [
                'code' => 'brother',
                'name' => 'Hermano',
                'status' => true,
            ],
            [
                'code' => 'sister',
                'name' => 'Hermana',
                'status' => true,
            ],
            [
                'code' => 'other',
                'name' => 'Otro',
                'status' => true,
            ],
        ];

        foreach ($parentescos as $parentesco) {
            DB::table('cat_parentescos')
                ->updateOrInsert(
                    ['code' => $parentesco['code']],
                    $parentesco
                );
        }
    }
}
