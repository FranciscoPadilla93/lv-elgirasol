<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = [
            [
                'code' => 'preschool',
                'name' => 'Preescolar',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'elementary',
                'name' => 'Primaria',
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($niveles as $nivel) {
            DB::table('cat_niveles') -> updateOrInsert(['code' => $nivel['code']], $nivel);
        }
    }
}
