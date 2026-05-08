<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_iniciales', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('inscripcion_id')->constrained('inscripciones');
            $table->foreignId('evaluated_by')->constrained('users');
            // INFORMACIÓN
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->date('evaluation_date');
            // RESULTADOS
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_approved')->default(false);
            // OBSERVACIONES
            $table->longText('observaciones')->nullable();
            // STATUS
            $table->boolean('status')->default(true);
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('inscripcion_id');
            $table->index('evaluated_by');
            $table->index('attempt');
            $table->index('is_approved');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_iniciales');
    }
};
