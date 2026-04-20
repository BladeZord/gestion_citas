<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}
