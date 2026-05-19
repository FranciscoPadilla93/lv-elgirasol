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
                'code' => 'cuota_recuperacion_preescolar_1_11',
                'name' => 'Cuota de recuperación Preescolar 1/11',
                'description' => 'Primera cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_2_11',
                'name' => 'Cuota de recuperación Preescolar 2/11',
                'description' => 'Segunda cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_3_11',
                'name' => 'Cuota de recuperación Preescolar 3/11',
                'description' => 'Tercera cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_4_11',
                'name' => 'Cuota de recuperación Preescolar 4/11',
                'description' => 'Cuarta cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_5_11',
                'name' => 'Cuota de recuperación Preescolar 5/11',
                'description' => 'Quinta cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_6_11',
                'name' => 'Cuota de recuperación Preescolar 6/11',
                'description' => 'Sexta cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_7_11',
                'name' => 'Cuota de recuperación Preescolar 7/11',
                'description' => 'Séptima cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_8_11',
                'name' => 'Cuota de recuperación Preescolar 8/11',
                'description' => 'Octava cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_9_11',
                'name' => 'Cuota de recuperación Preescolar 9/11',
                'description' => 'Novena cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_10_11',
                'name' => 'Cuota de recuperación Preescolar 10/11',
                'description' => 'Décima cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_preescolar_11_11',
                'name' => 'Cuota de recuperación Preescolar 11/11',
                'description' => 'Onceava cuota de recuperación de preescolar.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_1_11',
                'name' => 'Cuota de recuperación Primaria 1/11',
                'description' => 'Primera cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_2_11',
                'name' => 'Cuota de recuperación Primaria 2/11',
                'description' => 'Segunda cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_3_11',
                'name' => 'Cuota de recuperación Primaria 3/11',
                'description' => 'Tercera cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_4_11',
                'name' => 'Cuota de recuperación Primaria 4/11',
                'description' => 'Cuarta cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_5_11',
                'name' => 'Cuota de recuperación Primaria 5/11',
                'description' => 'Quinta cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_6_11',
                'name' => 'Cuota de recuperación Primaria 6/11',
                'description' => 'Sexta cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_7_11',
                'name' => 'Cuota de recuperación Primaria 7/11',
                'description' => 'Séptima cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_8_11',
                'name' => 'Cuota de recuperación Primaria 8/11',
                'description' => 'Octava cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_9_11',
                'name' => 'Cuota de recuperación Primaria 9/11',
                'description' => 'Novena cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_10_11',
                'name' => 'Cuota de recuperación Primaria 10/11',
                'description' => 'Décima cuota de recuperación de primaria.',
            ],
            [
                'code' => 'cuota_recuperacion_primaria_11_11',
                'name' => 'Cuota de recuperación Primaria 11/11',
                'description' => 'Onceava cuota de recuperación de primaria.',
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
