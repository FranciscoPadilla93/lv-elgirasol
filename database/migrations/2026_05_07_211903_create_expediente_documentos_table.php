<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_documentos', function (Blueprint $table) {
            $table->id();
            // RELACIONES
            $table->foreignId('expediente_id')->constrained('expedientes');
            $table->foreignId('tipo_documento_id')->constrained('cat_tipos_documento');
            // ARCHIVO
            $table->string('original_name', 255);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100)->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            // VALIDACIÓN
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->dateTime('validated_at')->nullable();
            $table->longText('validation_notes')->nullable();
            // STATUS
            $table->boolean('status')->default(true);
            // AUDITORÍA
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('expediente_id');
            $table->index('tipo_documento_id');
            $table->index('is_validated');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_documentos');
    }
};
