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
            // DOMICILIO
            $table->string('colonia', 500);
            $table->string('otra_colonia', 500)->nullable();
            $table->string('calle', 500);
            $table->string('numero_exterior', 20);
            $table->string('numero_interior', 20)->nullable();
            $table->string('codigo_postal', 5);
            // DATOS COMPLEMENTARIOS
            $table->text('procedencia_academica')->nullable();
            $table->enum('tipo_escuela', [
                'publica',
                'privada',
            ])->nullable();
            $table->text('motivo_cambio')->nullable();
            // CONSIDERACIONES MÉDICAS
            $table->boolean('alergias')->default(false);
            $table->string('alergias_descripcion', 250)->nullable();
            $table->boolean('enfermedad_cronica')->default(false);
            $table->string('enfermedad_cronica_descripcion', 250)->nullable();
            $table->foreignId('grupo_sanguineo_id')->nullable()->constrained('cat_grupo_sanguineo');
            $table->boolean('seguro_medico')->default(false);
            $table->foreignId('tipo_seguro_medico_id')->nullable()->constrained('cat_tipo_seguro_medico');
            $table->string('numero_poliza_seguro', 20)->nullable();
            // RELIGIÓN
            $table->string('religion', 250)->nullable();
            $table->boolean('bautizado')->default(false);
            $table->boolean('primera_comunion')->default(false);
            $table->boolean('confirmado')->default(false);
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
