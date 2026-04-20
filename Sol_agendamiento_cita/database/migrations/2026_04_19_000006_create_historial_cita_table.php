<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_cita', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')
                ->constrained('citas')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('estado_cita_id')
                ->constrained('estado_cita')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            $table->text('comentario')->nullable();
            $table->timestamp('fecha_cambio')->useCurrent();
            $table->timestamps();

            $table->index(['cita_id', 'fecha_cambio'], 'idx_historial_cita_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cita');
    }
};