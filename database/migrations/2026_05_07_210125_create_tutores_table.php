<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutores', function (Blueprint $table) {
            $table->id();
            // DATOS PERSONALES
            $table->string('nombre', 150);
            $table->string('apellido_paterno', 150);
            $table->string('apellido_materno', 150)->nullable();
            $table->string('correo', 255)->nullable();
            $table->string('telefono', 20);
            $table->string('telefono_secundario', 20)->nullable();
            $table->string('curp', 18)->nullable();
            // INFORMACIÓN LABORAL
            $table->string('empresa', 255)->nullable();
            $table->string('puesto', 255)->nullable();
            // INTRANET
            $table->foreignId('user_id')->nullable()->constrained('users');
            // OBSERVACIONES
            $table->longText('observaciones')->nullable();
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('correo');
            $table->index('telefono');
            $table->index('curp');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutores');
    }
};
