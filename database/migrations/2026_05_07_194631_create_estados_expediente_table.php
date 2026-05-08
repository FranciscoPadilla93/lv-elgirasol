<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_estados_expediente', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50) -> unique();
            $table->string('name', 100);
            // CONFIG
            $table->boolean('is_final') -> default(false);
            $table->boolean('allows_inscription') -> default(false);
            $table->boolean('status') -> default(true);
            $table->timestamps();
            $table->softDeletes();
            // INDICES
            $table->index('status');
            $table->index('is_final');
            $table->index('allows_inscription');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_expediente');
    }
};
