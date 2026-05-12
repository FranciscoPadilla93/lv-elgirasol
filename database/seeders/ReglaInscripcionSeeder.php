<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ReglaInscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = DB::table('cat_niveles')->pluck('id', 'code');
        $grados = DB::table('cat_grados')->pluck('id', 'code');
        $rules = [
            // PREESCOLAR
            [
                'nivel_id' => $niveles['preschool'],
                'grado_id' => $grados['1_preschool'],
                'is_new_admission' => true,
                'requires_evaluation' => false,
                'requires_socioeconomic_study' => true,
                'requires_treasury_validation' => true,
                'required_documents' => json_encode([
                    'birth_certificate',
                    'curp',
                    'medical_certificate',
                ]),
                'required_evaluations' => json_encode([
                    'academic',
                    'psychological'
                ]),
                'minimum_score' => null,
                'status' => true,
                'created_by' => 1,
            ],
            // PRIMARIA
            [
                'nivel_id' => $niveles['elementary'],
                'grado_id' => $grados['1_elementary'],
                'is_new_admission' => true,
                'requires_evaluation' => true,
                'requires_socioeconomic_study' => true,
                'requires_treasury_validation' => true,
                'required_documents' => json_encode([
                    'birth_certificate',
                    'curp',
                    'medical_certificate',
                    'address_proof',
                ]),
                'required_evaluations' => json_encode([
                    'academic',
                    'psychological'
                ]),
                'minimum_score' => 70,
                'status' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($rules as $rule) {
            DB::table('reglas_inscripcion')
                ->updateOrInsert(
                    [
                        'nivel_id' => $rule['nivel_id'],
                        'grado_id' => $rule['grado_id'],
                        'is_new_admission' => $rule['is_new_admission'],
                    ],
                    $rule
                );
        }
    }
}
