<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('expediente_id')->constrained('expedientes');
            $table->foreignId('ciclo_escolar_id')->constrained('cat_ciclos_escolares');
            $table->foreignId('nivel_id')->constrained('cat_niveles');
            $table->foreignId('grado_id')->constrained('cat_grados');
            $table->foreignId('estado_inscripcion_id')->constrained('cat_estados_inscripcion');
            // CONFIGURACIÓN
            $table->boolean('is_new_admission')->default(true);
            $table->dateTime('inscription_date')->nullable();
            // VALIDACIONES
            $table->boolean('requires_evaluation')->default(false);
            $table->boolean('requires_socioeconomic_study')->default(false);
            $table->boolean('requires_treasury_validation')->default(true);
            // RESULTADOS
            $table->boolean('evaluation_approved')->default(false);
            $table->boolean('socioeconomic_study_approved')->default(false);
            $table->boolean('treasury_approved')->default(false);
            // CONTROL
            $table->boolean('is_completed')->default(false);
            $table->longText('observaciones')->nullable();
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('expediente_id');
            $table->index('ciclo_escolar_id');
            $table->index('nivel_id');
            $table->index('grado_id');
            $table->index('estado_inscripcion_id');
            // EVITAR DUPLICADOS
            $table->unique([
                'expediente_id',
                'ciclo_escolar_id',
            ], 'uq_expediente_ciclo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
