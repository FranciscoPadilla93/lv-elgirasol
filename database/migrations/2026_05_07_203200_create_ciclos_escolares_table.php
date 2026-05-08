<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_ciclos_escolares', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50) -> unique();
            $table->string('name', 100);
            // FECHAS
            $table->date('start_date');
            $table->date('end_date');
            // FLAGS
            $table->boolean('is_current') -> default(false);
            $table->boolean('status') -> default(true);
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('status');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_escolares');
    }
};
