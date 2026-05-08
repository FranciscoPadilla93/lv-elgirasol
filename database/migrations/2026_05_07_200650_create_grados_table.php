<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_grados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_id') -> constrained('cat_niveles');
            $table->string('code', 50);
            $table->string('name', 100);
            // ORDEN
            $table->unsignedTinyInteger('order');
            $table->boolean('status') -> default(true);
            $table->timestamps();
            $table->softDeletes();
            // ÍNDICES
            $table->index('nivel_id');
            $table->index('status');
            $table->unique([
                'nivel_id',
                'code',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
