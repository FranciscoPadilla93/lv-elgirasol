<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100) -> unique();
            $table->string('name', 150);
            // CONFIGURACIÓN
            $table->boolean('is_required') -> default(false);
            $table->unsignedInteger('max_size_mb') -> default(10);
            $table->string('allowed_extensions', 255) -> default('pdf');
            $table->boolean('status') -> default(true);
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('status');
            $table->index('is_required');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento');
    }
};
