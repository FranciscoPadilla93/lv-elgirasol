<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $estados = [
            ['code' => 'AGS', 'name' => 'Aguascalientes', 'status' => true],
            ['code' => 'BC',  'name' => 'Baja California', 'status' => true],
            ['code' => 'BCS', 'name' => 'Baja California Sur', 'status' => true],
            ['code' => 'CAM', 'name' => 'Campeche', 'status' => true],
            ['code' => 'CHP', 'name' => 'Chiapas', 'status' => true],
            ['code' => 'CHH', 'name' => 'Chihuahua', 'status' => true],
            ['code' => 'CMX', 'name' => 'Ciudad de México', 'status' => true],
            ['code' => 'COA', 'name' => 'Coahuila de Zaragoza', 'status' => true],
            ['code' => 'COL', 'name' => 'Colima', 'status' => true],
            ['code' => 'DGO', 'name' => 'Durango', 'status' => true],
            ['code' => 'GTO', 'name' => 'Guanajuato', 'status' => true],
            ['code' => 'GRO', 'name' => 'Guerrero', 'status' => true],
            ['code' => 'HGO', 'name' => 'Hidalgo', 'status' => true],
            ['code' => 'JAL', 'name' => 'Jalisco', 'status' => true],
            ['code' => 'MEX', 'name' => 'México', 'status' => true],
            ['code' => 'MIC', 'name' => 'Michoacán de Ocampo', 'status' => true],
            ['code' => 'MOR', 'name' => 'Morelos', 'status' => true],
            ['code' => 'NAY', 'name' => 'Nayarit', 'status' => true],
            ['code' => 'NLE', 'name' => 'Nuevo León', 'status' => true],
            ['code' => 'OAX', 'name' => 'Oaxaca', 'status' => true],
            ['code' => 'PUE', 'name' => 'Puebla', 'status' => true],
            ['code' => 'QRO', 'name' => 'Querétaro', 'status' => true],
            ['code' => 'ROO', 'name' => 'Quintana Roo', 'status' => true],
            ['code' => 'SLP', 'name' => 'San Luis Potosí', 'status' => true],
            ['code' => 'SIN', 'name' => 'Sinaloa', 'status' => true],
            ['code' => 'SON', 'name' => 'Sonora', 'status' => true],
            ['code' => 'TAB', 'name' => 'Tabasco', 'status' => true],
            ['code' => 'TAM', 'name' => 'Tamaulipas', 'status' => true],
            ['code' => 'TLA', 'name' => 'Tlaxcala', 'status' => true],
            ['code' => 'VER', 'name' => 'Veracruz de Ignacio de la Llave', 'status' => true],
            ['code' => 'YUC', 'name' => 'Yucatán', 'status' => true],
            ['code' => 'ZAC', 'name' => 'Zacatecas', 'status' => true],
            ['code' => 'EXT', 'name' => 'Extranjero', 'status' => true],
        ];

        foreach ($estados as $estado) {
            $exists = DB::table('cat_estados')
                ->where('code', $estado['code'])
                ->exists();

            $data = [
                'code' => $estado['code'],
                'name' => $estado['name'],
                'status' => $estado['status'],
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            if (!$exists) {
                $data['created_at'] = $now;
            }

            DB::table('cat_estados')
                ->updateOrInsert(
                    ['code' => $estado['code']],
                    $data
                );
        }
    }
}
