<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'user_id',
        'tipo_cita_id',
        'fecha_cita',
        'hora_cita',
        'estado',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'hora_cita' => 'datetime:H:i:s',
    ];

    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    /**
     * Relación con tipo de cita
     */
    public function tipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'tipo_cita_id');
    }
}

