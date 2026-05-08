<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_tutores', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('expediente_id')->constrained('expedientes');
            $table->foreignId('tutor_id')->constrained('tutores');
            $table->foreignId('parentesco_id')->constrained('cat_parentescos');
            // CONFIGURACIÓN
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('is_financial_responsible')->default(false);
            $table->boolean('status')->default(true);
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('expediente_id');
            $table->index('tutor_id');
            $table->index('parentesco_id');
            // EVITAR DUPLICADOS
            $table->unique([
                'expediente_id',
                'tutor_id',
                'parentesco_id',
            ], 'uq_expediente_tutor_parentesco');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_tutores');
    }
};
