<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categorías médicas
        |--------------------------------------------------------------------------
        */
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);

            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
            $table->index('nombre');
        });

        /*
        |--------------------------------------------------------------------------
        | Tipos de pago
        |--------------------------------------------------------------------------
        */
        Schema::create('tipos_pago', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);

            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
        });

        /*
        |--------------------------------------------------------------------------
        | Estados de reserva
        |--------------------------------------------------------------------------
        | Ejemplos:
        | PENDIENTE
        | CONFIRMADA
        | ATENDIDA
        | CANCELADA
        |--------------------------------------------------------------------------
        */
        Schema::create('estados_reserva', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();

            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
        });

        /*
        |--------------------------------------------------------------------------
        | Pacientes
        |--------------------------------------------------------------------------
        */
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            /*
             * Corresponde al campo "no" de la tabla patient.
             * Puede representar número de historia clínica,
             * identificación o código interno.
             */
            $table->string('numero', 50)->unique();

            $table->string('nombre', 100);
            $table->string('apellido', 100);

            $table->char('genero', 1)->nullable();
            $table->date('fecha_nacimiento')->nullable();

            $table->string('correo', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('imagen', 255)->nullable();

            /*
             * Información clínica general proveniente del diagrama.
             */
            $table->text('enfermedades')->nullable();
            $table->text('medicamentos')->nullable();
            $table->text('alergias')->nullable();

            $table->boolean('es_favorito')->default(false);

            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
            $table->index('correo');
            $table->index(['apellido', 'nombre']);
        });

        /*
        |--------------------------------------------------------------------------
        | Médicos
        |--------------------------------------------------------------------------
        */
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();

            /*
             * Corresponde al campo "no" de la tabla medic.
             * Puede utilizarse como código o número profesional.
             */
            $table->string('numero', 50)->unique();

            $table->string('nombre', 100);
            $table->string('apellido', 100);

            $table->char('genero', 1)->nullable();
            $table->date('fecha_nacimiento')->nullable();

            $table->string('correo', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('imagen', 255)->nullable();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete();

            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
            $table->index('correo');
            $table->index(['apellido', 'nombre']);
        });

        /*
        |--------------------------------------------------------------------------
        | Reservas
        |--------------------------------------------------------------------------
        */
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->string('titulo', 100);
            $table->text('nota')->nullable();
            $table->text('mensaje')->nullable();

            /*
             * En MySQL estos campos estaban como VARCHAR.
             * En PostgreSQL deben manejar tipos fecha y hora.
             */
            $table->date('fecha_reserva');
            $table->time('hora_reserva');

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->restrictOnDelete();

            $table->foreignId('medico_id')
                ->constrained('medicos')
                ->restrictOnDelete();

            /*
             * Usuario que registró o administra la reserva.
             */
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->foreignId('tipo_pago_id')
                ->nullable()
                ->constrained('tipos_pago')
                ->restrictOnDelete();

            $table->foreignId('estado_reserva_id')
                ->constrained('estados_reserva')
                ->restrictOnDelete();

            $table->text('sintomas')->nullable();
            $table->text('enfermedad')->nullable();
            $table->text('medicamentos')->nullable();

            /*
             * Para valores monetarios no debe utilizarse DOUBLE.
             */
            $table->decimal('precio', 12, 2)->default(0);

            /*
             * Reemplaza el TINYINT de MySQL.
             */
            $table->boolean('es_web')->default(false);

            /*
             * Estado de auditoría:
             * A = Activo
             * I = Inactivo
             * E = Eliminado
             */
            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            $table->foreignId('usuario_creacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->timestampTz('fecha_actualizacion')->nullable();

            $table->foreignId('usuario_actualizacion')
                ->nullable()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->index('estado');
            $table->index('fecha_reserva');
            $table->index('estado_reserva_id');
            $table->index('paciente_id');
            $table->index('medico_id');

            /*
             * Ayuda a consultar rápidamente la agenda diaria
             * de cada médico.
             */
            $table->index(
                ['medico_id', 'fecha_reserva', 'hora_reserva'],
                'reservas_medico_fecha_hora_index'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Restricciones de estados de auditoría
        |--------------------------------------------------------------------------
        */
        $this->agregarRestriccionEstado('categorias');
        $this->agregarRestriccionEstado('tipos_pago');
        $this->agregarRestriccionEstado('estados_reserva');
        $this->agregarRestriccionEstado('pacientes');
        $this->agregarRestriccionEstado('medicos');
        $this->agregarRestriccionEstado('reservas');

        /*
        |--------------------------------------------------------------------------
        | Restricciones adicionales
        |--------------------------------------------------------------------------
        */
        DB::statement("
            ALTER TABLE pacientes
            ADD CONSTRAINT pacientes_genero_check
            CHECK (
                genero IS NULL
                OR genero IN ('M', 'F', 'O')
            )
        ");

        DB::statement("
            ALTER TABLE medicos
            ADD CONSTRAINT medicos_genero_check
            CHECK (
                genero IS NULL
                OR genero IN ('M', 'F', 'O')
            )
        ");

        DB::statement("
            ALTER TABLE reservas
            ADD CONSTRAINT reservas_precio_check
            CHECK (precio >= 0)
        ");
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        /*
         * El orden es importante debido a las claves foráneas.
         */
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('medicos');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('estados_reserva');
        Schema::dropIfExists('tipos_pago');
        Schema::dropIfExists('categorias');
    }

    /**
     * Agrega el CHECK estándar de auditoría.
     */
    private function agregarRestriccionEstado(string $tabla): void
    {
        DB::statement("
            ALTER TABLE {$tabla}
            ADD CONSTRAINT {$tabla}_estado_check
            CHECK (estado IN ('A', 'I', 'E'))
        ");
    }
};
