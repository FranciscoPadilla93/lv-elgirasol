<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reglas_inscripcion', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('nivel_id')->constrained('cat_niveles');
            $table->foreignId('grado_id')->constrained('cat_grados');
            // CONFIGURACIÓN
            $table->boolean('is_new_admission')->default(true);
            // VALIDACIONES
            $table->boolean('requires_evaluation')->default(false);
            $table->boolean('requires_socioeconomic_study')->default(false);
            $table->boolean('requires_treasury_validation')->default(true);
            // DOCUMENTOS
            $table->json('required_documents')->nullable();
            // EVALUACIONES
            $table->json('required_evaluations')->nullable();
            // CONFIG EXTRA
            $table->unsignedTinyInteger('minimum_score')->nullable();
            $table->boolean('status')->default(true);
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('nivel_id');
            $table->index('grado_id');
            $table->index('is_new_admission');
            $table->index('requires_evaluation');
            $table->index('status');
            // EVITAR DUPLICADOS
            $table->unique([
                'nivel_id',
                'grado_id',
                'is_new_admission',
            ], 'uq_regla_nivel_grado_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_inscripcion');
    }
};
