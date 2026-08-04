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
        | Usuarios
        |--------------------------------------------------------------------------
        */
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();

            $table->string('usuario', 100)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('correo', 150)->unique();

            // Debe almacenar la contraseña cifrada mediante Hash::make().
            $table->string('contrasenia', 255);

            /*
             * Estados:
             * A = Activo
             * I = Inactivo
             * E = Eliminado
             */
            $table->char('estado', 1)->default('A');

            $table->timestampTz('fecha_creacion')->useCurrent();

            /*
             * Nullable para permitir la creación del primer usuario
             * del sistema mediante un seeder.
             */
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
        | Perfiles
        |--------------------------------------------------------------------------
        */
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 150);
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
        | Relación usuario-perfil
        |--------------------------------------------------------------------------
        | Permite que un usuario tenga uno o varios perfiles.
        */
        Schema::create('usuario_perfil', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->foreignId('perfil_id')
                ->constrained('perfiles')
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

            /*
             * Evita asignar dos veces el mismo perfil
             * al mismo usuario.
             */
            $table->unique(
                ['usuario_id', 'perfil_id'],
                'usuario_perfil_usuario_id_perfil_id_unique'
            );

            $table->index('estado');
        });

        /*
        |--------------------------------------------------------------------------
        | Historial de contraseñas
        |--------------------------------------------------------------------------
        */
        Schema::create('historial_contrasenias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->restrictOnDelete();

            /*
             * Aquí también se guarda el hash de la contraseña,
             * nunca la contraseña original.
             */
            $table->string('contrasenia', 255);

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

            $table->index(
                ['usuario_id', 'estado'],
                'historial_contrasenias_usuario_estado_index'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Sesiones
        |--------------------------------------------------------------------------
        | Tabla técnica utilizada para almacenar sesiones de Laravel.
        */
        Schema::create('sesiones', function (Blueprint $table) {
            $table->string('id')->primary();

            /*
             * Se conserva el nombre user_id porque Laravel lo utiliza
             * internamente en el manejador de sesiones de base de datos.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('usuarios')
                ->restrictOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        /*
        |--------------------------------------------------------------------------
        | Restricciones de estados para PostgreSQL
        |--------------------------------------------------------------------------
        */
        DB::statement("
            ALTER TABLE usuarios
            ADD CONSTRAINT usuarios_estado_check
            CHECK (estado IN ('A', 'I', 'E'))
        ");

        DB::statement("
            ALTER TABLE perfiles
            ADD CONSTRAINT perfiles_estado_check
            CHECK (estado IN ('A', 'I', 'E'))
        ");

        DB::statement("
            ALTER TABLE usuario_perfil
            ADD CONSTRAINT usuario_perfil_estado_check
            CHECK (estado IN ('A', 'I', 'E'))
        ");

        DB::statement("
            ALTER TABLE historial_contrasenias
            ADD CONSTRAINT historial_contrasenias_estado_check
            CHECK (estado IN ('A', 'I', 'E'))
        ");
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        /*
         * Se eliminan en orden inverso para no generar errores
         * por las claves foráneas.
         */
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('historial_contrasenias');
        Schema::dropIfExists('usuario_perfil');
        Schema::dropIfExists('perfiles');
        Schema::dropIfExists('usuarios');
    }
};
