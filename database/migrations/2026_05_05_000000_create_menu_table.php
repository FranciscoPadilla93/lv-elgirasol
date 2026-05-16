<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();

            $table->foreignId('module_id')
                ->nullable()
                ->constrained('modules')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('menu')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('label', 255);
            $table->string('path', 255)->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('order')->nullable();
            $table->string('status', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
