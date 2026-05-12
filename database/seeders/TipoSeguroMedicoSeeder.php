<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoSeguroMedicoSeeder extends Seeder
{
    public function run(): void
    {
        $seguros = [
            [
                'code' => 'ISSSTE',
                'name' => 'ISSSTE',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'IMSS',
                'name' => 'IMSS',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'PRIVADO',
                'name' => 'Privado',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'OTRO',
                'name' => 'Otro',
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($seguros as $seguro) {
            DB::table('cat_tipo_seguro_medico')
                ->updateOrInsert(
                    ['code' => $seguro['code']],
                    $seguro
                );
        }
    }
}
