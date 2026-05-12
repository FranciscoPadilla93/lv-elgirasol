<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoEvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'code' => 'psychological',
                'name' => 'Psicológica',
                'description' => 'Evaluación psicológica inicial del aspirante.',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'academic',
                'name' => 'Académica',
                'description' => 'Evaluación académica o escolar inicial del aspirante.',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('cat_tipos_evaluacion')->updateOrInsert(
                ['code' => $tipo['code']],
                $tipo
            );
        }
    }
}
