<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $documentos = [
            [
                'code' => 'birth_certificate',
                'name' => 'Acta de nacimiento',
                'is_required' => false,
            ],
            [
                'code' => 'curp',
                'name' => 'CURP',
                'is_required' => false,
            ],
            [
                'code' => 'medical_certificate',
                'name' => 'Certificado médico',
                'is_required' => false,
            ],
            [
                'code' => 'vaccination_card',
                'name' => 'Cartilla de vacunación',
                'is_required' => false,
            ],
            [
                'code' => 'father_ine',
                'name' => 'INE Papá',
                'is_required' => false,
            ],
            [
                'code' => 'mother_ine',
                'name' => 'INE Mamá',
                'is_required' => false,
            ],
            [
                'code' => 'address_proof',
                'name' => 'Comprobante de domicilio',
                'is_required' => false,
            ],
            [
                'code' => 'preschool_document',
                'name' => 'Documento preescolar',
                'is_required' => false,
            ],
            [
                'code' => 'school_withdrawal',
                'name' => 'Baja escuela anterior',
                'is_required' => false,
            ],
            [
                'code' => 'last_grade_proof',
                'name' => 'Comprobante último grado',
                'is_required' => false,
            ],
        ];

        foreach ($documentos as $documento) {
            DB::table('cat_tipos_documento')
                ->updateOrInsert(
                    ['code' => $documento['code']],
                    [
                        ...$documento,
                        'max_size_mb' => 10,
                        'allowed_extensions' => 'pdf',
                        'status' => true,
                    ]
                );
        }
    }
}
