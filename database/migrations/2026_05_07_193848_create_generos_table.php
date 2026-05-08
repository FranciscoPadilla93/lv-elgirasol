<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_generos', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50) -> unique();
            $table->string('name', 100);
            $table->string('status', 20) -> default('active');
            $table->timestamps();
            $table->softDeletes();

            // ÍNDICES
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generos');
    }
};
