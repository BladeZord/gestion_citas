<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('tipo_cita_id')->constrained('tipos_cita')->onDelete('cascade');
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->timestamps();
            
            // Índice compuesto para validar citas duplicadas
            $table->unique(['user_id', 'fecha_cita', 'hora_cita'], 'unique_user_fecha_hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};

