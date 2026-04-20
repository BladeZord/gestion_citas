<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'cliente_id',
        'usuario_id',
        'fecha',
        'hora',
        'motivo',
        'estado_cita_id',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCita::class, 'estado_cita_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialCita::class, 'cita_id');
    }
}
