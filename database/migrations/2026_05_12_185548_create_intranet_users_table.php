<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intranet_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique();
            $table->string('full_name', 1500);
            $table->string('curp', 18)->unique();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intranet_users');
    }
};
