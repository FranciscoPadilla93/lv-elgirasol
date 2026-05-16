<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoContactoSeeder extends Seeder
{
    public function run(): void
    {
        $tipo_contactos = [
            [
                'code' => '1_contacto',
                'name' => '1er contacto',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => '2_contacto',
                'name' => '2do contacto',
                'status' => true,
                'created_at' =>  now()
            ],
             [
                'code' => '3_contacto',
                'name' => '3er contacto',
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($tipo_contactos as $item) {
            DB::table('cat_tipo_contacto') -> updateOrInsert(['code' => $item['code']], $item);
        }
    }
}
