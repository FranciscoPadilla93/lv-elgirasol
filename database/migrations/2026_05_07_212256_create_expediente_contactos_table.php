<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_contactos', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('expediente_id')->constrained('expedientes');
            $table->foreignId('parentesco_id')->constrained('cat_parentescos');
            // DATOS PERSONALES
            $table->string('nombre', 150);
            $table->string('apellido_paterno', 150);
            $table->string('apellido_materno', 150)->nullable();
            // CONTACTO
            $table->string('telefono', 20);
            $table->string('telefono_secundario', 20)->nullable();
            $table->string('correo', 255)->nullable();
            // CONFIGURACIÓN
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_authorized_pickup')->default(false);
            $table->boolean('status')->default(true);
            // OBSERVACIONES
            $table->longText('observaciones')->nullable();
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('expediente_id');
            $table->index('parentesco_id');
            $table->index('telefono');
            $table->index('is_emergency_contact');
            $table->index('is_authorized_pickup');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_contactos');
    }
};
