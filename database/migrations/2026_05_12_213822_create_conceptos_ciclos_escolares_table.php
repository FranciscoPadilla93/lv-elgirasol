<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conceptos_ciclos_escolares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concepto_id')->constrained('conceptos');
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares');
            $table->decimal('price', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('has_late_fee')->default(false);
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(
                [
                    'concepto_id',
                    'ciclo_escolar_id',
                ],
                'concepto_ciclo_unique'
            );
            $table->index('concepto_id');
            $table->index('ciclo_escolar_id');
            $table->index('status');
            $table->index('has_late_fee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conceptos_ciclos_escolares');
    }
};
