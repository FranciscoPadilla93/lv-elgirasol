<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School\Concepto;

class ConceptoSeeder extends Seeder
{
    public function run(): void
    {
        $conceptos = [
            [
                'code' => 'inscripcion_primaria',
                'name' => 'Inscripción Primaria',
                'description' => 'Concepto de inscripción para nivel primaria.',
            ],
            [
                'code' => 'inscripcion_preescolar',
                'name' => 'Inscripción Preescolar',
                'description' => 'Concepto de inscripción para nivel preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_1_11',
                'name' => 'Colegiatura Preescolar 1/11',
                'description' => 'Primera colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_2_11',
                'name' => 'Colegiatura Preescolar 2/11',
                'description' => 'Segunda colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_3_11',
                'name' => 'Colegiatura Preescolar 3/11',
                'description' => 'Tercera colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_4_11',
                'name' => 'Colegiatura Preescolar 4/11',
                'description' => 'Cuarta colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_5_11',
                'name' => 'Colegiatura Preescolar 5/11',
                'description' => 'Quinta colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_6_11',
                'name' => 'Colegiatura Preescolar 6/11',
                'description' => 'Sexta colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_7_11',
                'name' => 'Colegiatura Preescolar 7/11',
                'description' => 'Séptima colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_8_11',
                'name' => 'Colegiatura Preescolar 8/11',
                'description' => 'Octava colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_9_11',
                'name' => 'Colegiatura Preescolar 9/11',
                'description' => 'Novena colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_10_11',
                'name' => 'Colegiatura Preescolar 10/11',
                'description' => 'Décima colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_preescolar_11_11',
                'name' => 'Colegiatura Preescolar 11/11',
                'description' => 'Onceava colegiatura de preescolar.',
            ],
            [
                'code' => 'colegiatura_primaria_1_11',
                'name' => 'Colegiatura Primaria 1/11',
                'description' => 'Primera colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_2_11',
                'name' => 'Colegiatura Primaria 2/11',
                'description' => 'Segunda colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_3_11',
                'name' => 'Colegiatura Primaria 3/11',
                'description' => 'Tercera colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_4_11',
                'name' => 'Colegiatura Primaria 4/11',
                'description' => 'Cuarta colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_5_11',
                'name' => 'Colegiatura Primaria 5/11',
                'description' => 'Quinta colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_6_11',
                'name' => 'Colegiatura Primaria 6/11',
                'description' => 'Sexta colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_7_11',
                'name' => 'Colegiatura Primaria 7/11',
                'description' => 'Séptima colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_8_11',
                'name' => 'Colegiatura Primaria 8/11',
                'description' => 'Octava colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_9_11',
                'name' => 'Colegiatura Primaria 9/11',
                'description' => 'Novena colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_10_11',
                'name' => 'Colegiatura Primaria 10/11',
                'description' => 'Décima colegiatura de primaria.',
            ],
            [
                'code' => 'colegiatura_primaria_11_11',
                'name' => 'Colegiatura Primaria 11/11',
                'description' => 'Onceava colegiatura de primaria.',
            ],
            [
                'code' => 'guarderia',
                'name' => 'Guardería',
                'description' => 'Guardería',
            ],
            [
                'code' => 'guitarra',
                'name' => 'Guitarra',
                'description' => 'Guitarra',
            ],
            [
                'code' => 'violin',
                'name' => 'Violín',
                'description' => 'Violín',
            ],
            [
                'code' => 'deportes',
                'name' => 'Deportes',
                'description' => 'Deportes',
            ],
            [
                'code' => 'arte',
                'name' => 'Arte',
                'description' => 'Arte',
            ],
            [
                'code' => 'credencial',
                'name' => 'Credencial',
                'description' => 'Credencial',
            ],
            [
                'code' => 'constancias',
                'name' => 'Constancias',
                'description' => 'Constancias',
            ],
            [
                'code' => 'penalizacion',
                'name' => 'Penalización',
                'description' => 'Penalización',
            ],
            [
                'code' => 'corresponsabilidad',
                'name' => 'Corresponsabilidad',
                'description' => 'Corresponsabilidad',
            ],
        ];

        foreach ($conceptos as $concepto) {
            Concepto::withTrashed()->updateOrCreate(
                [
                    'code' => $concepto['code'],
                ],
                [
                    'name' => $concepto['name'],
                    'description' => $concepto['description'],
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                ]
            )->restore();
        }
    }
}
