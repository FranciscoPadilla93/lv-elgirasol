<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EstadoInscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            [
                'code' => 'pending',
                'name' => 'Pendiente',
                'is_final' => false,
                'status' => true,
            ],
            [
                'code' => 'documents_pending',
                'name' => 'Documentos pendientes',
                'is_final' => false,
                'status' => true,
            ],
            [
                'code' => 'evaluation_pending',
                'name' => 'Evaluación pendiente',
                'is_final' => false,
                'status' => true,
            ],
            [
                'code' => 'payment_pending',
                'name' => 'Pago pendiente',
                'is_final' => false,
                'status' => true,
            ],
            [
                'code' => 'approved',
                'name' => 'Aprobada',
                'is_final' => false,
                'status' => true,
            ],
            [
                'code' => 'completed',
                'name' => 'Finalizada',
                'is_final' => true,
                'status' => true,
            ],
            [
                'code' => 'cancelled',
                'name' => 'Cancelada',
                'is_final' => true,
                'status' => true,
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('cat_estados_inscripcion')
                ->updateOrInsert(
                    ['code' => $estado['code']],
                    $estado
                );
        }
    }
}
