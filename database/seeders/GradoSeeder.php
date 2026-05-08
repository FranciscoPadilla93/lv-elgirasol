<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradoSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = DB::table('cat_niveles') -> pluck('id', 'code');

        $grados = [
            /*
            |--------------------------------------------------------------------------
            | PREESCOLAR
            |--------------------------------------------------------------------------
            */
            [
                'nivel_id' => $niveles['preschool'],
                'code' => '1_preschool',
                'name' => '1° Preescolar',
                'order' => 1,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['preschool'],
                'code' => '2_preschool',
                'name' => '2° Preescolar',
                'order' => 2,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['preschool'],
                'code' => '3_preschool',
                'name' => '3° Preescolar',
                'order' => 3,
                'status' => true,
            ],
            /*
            |--------------------------------------------------------------------------
            | PRIMARIA
            |--------------------------------------------------------------------------
            */
            [
                'nivel_id' => $niveles['elementary'],
                'code' => '1_elementary',
                'name' => '1° Primaria',
                'order' => 1,
                'status' => true,
            ],

            [
                'nivel_id' => $niveles['elementary'],
                'code' => '2_elementary',
                'name' => '2° Primaria',
                'order' => 2,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['elementary'],
                'code' => '3_elementary',
                'name' => '3° Primaria',
                'order' => 3,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['elementary'],
                'code' => '4_elementary',
                'name' => '4° Primaria',
                'order' => 4,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['elementary'],
                'code' => '5_elementary',
                'name' => '5° Primaria',
                'order' => 5,
                'status' => true,
            ],
            [
                'nivel_id' => $niveles['elementary'],
                'code' => '6_elementary',
                'name' => '6° Primaria',
                'order' => 6,
                'status' => true,
            ],
        ];

        foreach ($grados as $grado) {
            DB::table('cat_grados')
                ->updateOrInsert(
                    [
                        'nivel_id' => $grado['nivel_id'],
                        'code' => $grado['code'],
                    ],
                    $grado
                );
        }
    }
}
