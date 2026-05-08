<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_estados_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            // FLAGS
            $table->boolean('is_final')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('status');
            $table->index('is_final');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_inscripcion');
    }
};
