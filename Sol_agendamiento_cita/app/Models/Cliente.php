<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'fecha_nacimiento',
        'estado',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'cliente_id');
    }
}
