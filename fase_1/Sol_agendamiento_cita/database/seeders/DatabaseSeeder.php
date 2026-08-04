<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\TipoCita;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios iniciales
        Usuario::create([
            'nombre' => 'Administrador',
            'correo' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'rol' => 'admin',
        ]);

        Usuario::create([
            'nombre' => 'Usuario Test',
            'correo' => 'usuario@example.com',
            'password' => Hash::make('password123'),
            'rol' => 'usuario',
        ]);

        // Crear tipos de cita por defecto
        TipoCita::create(['nombre' => 'Consulta General']);
        TipoCita::create(['nombre' => 'Consulta Especializada']);
        TipoCita::create(['nombre' => 'Revisión']);
        TipoCita::create(['nombre' => 'Seguimiento']);
    }
}

