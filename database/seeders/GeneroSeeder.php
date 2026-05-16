<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneroSeeder extends Seeder
{
    public function run(): void
    {
         $generos = [
            [
                'code' => 'male',
                'name' => 'Masculino',
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => 'female',
                'name' => 'Femenino',
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($generos as $genero) {
            DB::table('cat_generos') -> updateOrInsert(['code' => $genero['code']], $genero);
        }
    }
}
