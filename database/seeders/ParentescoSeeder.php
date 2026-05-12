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
                'created_at' =>  now()
            ],
            [
                'code' => 'father',
                'name' => 'Padre',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'guardian',
                'name' => 'Tutor',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'grandmother',
                'name' => 'Abuela',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'grandfather',
                'name' => 'Abuelo',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'uncle',
                'name' => 'Tío',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'aunt',
                'name' => 'Tía',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'brother',
                'name' => 'Hermano',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'sister',
                'name' => 'Hermana',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'other',
                'name' => 'Otro',
                'status' => true,
                'created_at' =>  now()
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
