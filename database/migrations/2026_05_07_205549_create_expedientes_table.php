<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            // IDENTIFICACIÓN
            $table->string('folio', 50)->unique();
            //DATOS PERSONALES
            $table->string('nombre', 150);
            $table->string('apellido_paterno', 150);
            $table->string('apellido_materno', 150)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('curp', 18)->nullable()->unique();
            $table->foreignId('genero_id')->constrained('cat_generos');
            // ESTATUS EXPEDIENTE
            $table->foreignId('estado_expediente_id')->constrained('cat_estados_expediente');
            // INFORMACIÓN GENERAL
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->text('motivo_baja')->nullable();
            $table->longText('observaciones')->nullable();
            //ARCHIVOS
            $table->string('foto_path', 500)->nullable();
            //AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            //ÍNDICES
            $table->index('nombre');
            $table->index('apellido_paterno');
            $table->index('fecha_nacimiento');
            $table->index('estado_expediente_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
