<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // CORE
            RoleSeeder::class,
            ModuleSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            // LOGICA DEL NEGOCIO
            GeneroSeeder::class,
            EstadoExpedienteSeeder::class,
            NivelSeeder::class,
            GradoSeeder::class,
            CicloEscolarSeeder::class,
            ParentescoSeeder::class,
            TipoDocumentoSeeder::class,
            EstadoInscripcionSeeder::class,
            ReglaInscripcionSeeder::class,
        ]);
    }
}
