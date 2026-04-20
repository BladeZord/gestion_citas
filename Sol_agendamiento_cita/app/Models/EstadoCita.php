<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCita extends Model
{
    protected $table = 'estado_cita';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'estado',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'estado_cita_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialCita::class, 'estado_cita_id');
    }
}
