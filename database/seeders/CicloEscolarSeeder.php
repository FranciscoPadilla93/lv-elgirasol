<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CicloEscolarSeeder extends Seeder
{
    public function run(): void
    {
        $ciclos = [
            [
                'code' => '2025-2026',
                'name' => 'Ciclo Escolar 2025 - 2026',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'is_current' => false,
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => '2026-2027',
                'name' => 'Ciclo Escolar 2026 - 2027',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_current' => true,
                'status' => true,
                'created_at' =>  now()
            ],
            [
                'code' => '2027-2028',
                'name' => 'Ciclo Escolar 2027 - 2028',
                'start_date' => '2027-01-01',
                'end_date' => '2027-12-31',
                'is_current' => false,
                'status' => true,
                'created_at' =>  now()
            ],
        ];

        foreach ($ciclos as $ciclo) {
            DB::table('ciclos_escolares')
                ->updateOrInsert(
                    ['code' => $ciclo['code']],
                    $ciclo
                );
        }
    }
}
