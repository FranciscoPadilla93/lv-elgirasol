<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            [
                'code' => 'prospect',
                'name' => 'Prospecto',
                'is_final' => false,
                'allows_inscription' => false,
                'status' => true,
            ],
            [
                'code' => 'in_progress',
                'name' => 'En proceso',
                'is_final' => false,
                'allows_inscription' => false,
                'status' => true,
            ],
            [
                'code' => 'evaluation',
                'name' => 'Evaluación',
                'is_final' => false,
                'allows_inscription' => false,
                'status' => true,
            ],
            [
                'code' => 'approved',
                'name' => 'Aprobado',
                'is_final' => false,
                'allows_inscription' => true,
                'status' => true,
            ],
            [
                'code' => 'enrolled',
                'name' => 'Inscrito',
                'is_final' => false,
                'allows_inscription' => true,
                'status' => true,
            ],
            [
                'code' => 'active_student',
                'name' => 'Alumno Activo',
                'is_final' => false,
                'allows_inscription' => true,
                'status' => true,
            ],
            [
                'code' => 'inactive',
                'name' => 'Inactivo',
                'is_final' => false,
                'allows_inscription' => false,
                'status' => true,
            ],
            [
                'code' => 'dropped',
                'name' => 'Baja',
                'is_final' => true,
                'allows_inscription' => false,
                'status' => true,
            ],
            [
                'code' => 'graduated',
                'name' => 'Egresado',
                'is_final' => true,
                'allows_inscription' => false,
                'status' => true,
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('cat_estados_expediente') -> updateOrInsert(['code' => $estado['code']], $estado);
        }
    }
}
