<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')
                ->constrained('roles')
                ->noActionOnDelete()
                ->cascadeOnUpdate();
            $table->string('nombre', 150);
            $table->string('correo', 150)->unique();
            $table->string('password');
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
