<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('estado_cita_id')
                ->constrained('estado_cita')
                ->noActionOnDelete()
                ->cascadeOnUpdate();
            $table->date('fecha');
            $table->time('hora');
            $table->text('motivo')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['cliente_id', 'fecha', 'hora'], 'uk_cita_cliente_fecha_hora');
            $table->index(['usuario_id', 'fecha'], 'idx_citas_usuario_fecha');
            $table->index(['estado_cita_id'], 'idx_citas_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
