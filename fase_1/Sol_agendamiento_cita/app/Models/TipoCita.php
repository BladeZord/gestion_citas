<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCita extends Model
{
    use HasFactory;

    protected $table = 'tipos_cita';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación con citas
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'tipo_cita_id');
    }
}

