<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'id',
        'nombre',
        'correo',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
    ];

    // No usar 'hashed' cast porque las contraseñas ya vienen hasheadas de la BD
    // Solo hasheamos cuando se crea/actualiza, no cuando se lee
    protected $casts = [
        // 'password' => 'hashed', // Removido para evitar problemas al leer desde BD
    ];

    /**
     * Mutator para hashear la contraseña al asignarla
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Relación con citas
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'user_id');
    }
}

