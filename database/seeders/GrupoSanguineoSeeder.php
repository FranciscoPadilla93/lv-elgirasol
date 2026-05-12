<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GrupoSanguineoSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = [
            [
                'code' => 'O+',
                'name' => 'O+',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'O-',
                'name' => 'O-',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'A+',
                'name' => 'A+',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'A-',
                'name' => 'A-',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'B+',
                'name' => 'B+',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'B-',
                'name' => 'B-',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'AB+',
                'name' => 'AB+',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'AB-',
                'name' => 'AB-',
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($grupos as $grupo) {
            DB::table('cat_grupo_sanguineo')
                ->updateOrInsert(
                    ['code' => $grupo['code']],
                    $grupo
                );
        }
    }
}
