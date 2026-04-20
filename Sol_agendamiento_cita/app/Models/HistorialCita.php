<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCita extends Model
{
    protected $table = 'historial_cita';

    protected $fillable = [
        'cita_id',
        'estado_cita_id',
        'observacion',
        'fecha',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCita::class, 'estado_cita_id');
    }
}
