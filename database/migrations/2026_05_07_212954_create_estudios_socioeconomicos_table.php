<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudios_socioeconomicos', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('inscripcion_id')->constrained('inscripciones');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            // INFORMACIÓN
            $table->boolean('submitted_by_tutor')->default(false);
            $table->dateTime('submitted_at')->nullable();
            // RESPUESTAS
            $table->json('responses')->nullable();
            // VALIDACIÓN
            $table->boolean('is_approved')->default(false);
            $table->dateTime('approved_at')->nullable();
            $table->longText('approval_notes')->nullable();
            // STATUS
            $table->boolean('status')->default(true);
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('inscripcion_id');
            $table->index('approved_by');
            $table->index('submitted_by_tutor');
            $table->index('is_approved');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudios_socioeconomicos');
    }
};
