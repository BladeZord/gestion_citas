<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\EstadoCita;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Rol::create([
            'nombre' => 'Administrador',
            'codigo' => 'admin',
            'descripcion' => 'Acceso total al sistema',
            'estado' => true,
        ]);

        $medicoRole = Rol::create([
            'nombre' => 'Médico',
            'codigo' => 'medico',
            'descripcion' => 'Gestión de citas y atención médica',
            'estado' => true,
        ]);

        $recepcionRole = Rol::create([
            'nombre' => 'Recepcionista',
            'codigo' => 'recepcionista',
            'descripcion' => 'Gestión de clientes y citas',
            'estado' => true,
        ]);

        $usuarioRole = Rol::create([
            'nombre' => 'Usuario',
            'codigo' => 'usuario',
            'descripcion' => 'Usuario básico del sistema',
            'estado' => true,
        ]);

        // Crear estados de cita
        EstadoCita::firstOrCreate(
            ['codigo' => 'PENDIENTE'],
            [
                'nombre' => 'Pendiente',
                'descripcion' => 'La cita fue registrada y está pendiente de atención',
                'estado' => true,
            ]
        );

        EstadoCita::firstOrCreate(
            ['codigo' => 'CONFIRMADA'],
            [
                'nombre' => 'Confirmada',
                'descripcion' => 'La cita fue confirmada',
                'estado' => true,
            ]
        );

        EstadoCita::firstOrCreate(
            ['codigo' => 'EN_ATENCION'],
            [
                'nombre' => 'En atención',
                'descripcion' => 'La cita está siendo atendida',
                'estado' => true,
            ]
        );

        EstadoCita::firstOrCreate(
            ['codigo' => 'FINALIZADA'],
            [
                'nombre' => 'Finalizada',
                'descripcion' => 'La cita fue completada',
                'estado' => true,
            ]
        );

        EstadoCita::firstOrCreate(
            ['codigo' => 'CANCELADA'],
            [
                'nombre' => 'Cancelada',
                'descripcion' => 'La cita fue cancelada',
                'estado' => true,
            ]
        );

        // Crear usuarios iniciales
        Usuario::create([
            'nombre' => 'Administrador',
            'correo' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'rol_id' => $adminRole->id,
            'estado' => true,
        ]);

        Usuario::create([
            'nombre' => 'Dr. Juan Pérez',
            'correo' => 'medico@example.com',
            'password' => Hash::make('password123'),
            'rol_id' => $medicoRole->id,
            'estado' => true,
        ]);

        Usuario::create([
            'nombre' => 'Recepción Principal',
            'correo' => 'recepcion@example.com',
            'password' => Hash::make('password123'),
            'rol_id' => $recepcionRole->id,
            'estado' => true,
        ]);

        Usuario::create([
            'nombre' => 'Usuario Test',
            'correo' => 'usuario@example.com',
            'password' => Hash::make('password123'),
            'rol_id' => $usuarioRole->id,
            'estado' => true,
        ]);
    }
}
