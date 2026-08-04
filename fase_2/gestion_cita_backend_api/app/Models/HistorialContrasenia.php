<?php

namespace App\Models;

use App\Model\Constants\EstadosAuditoria;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'usuario_id',
    'contrasenia',
    'estado',
    'usuario_creacion',
    'usuario_actualizacion',
])]
#[Hidden([
    'contrasenia',
])]
class HistorialContrasenia extends Model
{
    use HasFactory;

    protected $table = 'historial_contrasenias';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'fecha_creacion';

    public const UPDATED_AT = 'fecha_actualizacion';

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id'
        );
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_creacion'
        );
    }

    public function usuarioActualizador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_actualizacion'
        );
    }

    public function estaActivo(): bool
    {
        return $this->estado === EstadosAuditoria::ACTIVO;
    }

    public function estaInactivo(): bool
    {
        return $this->estado === EstadosAuditoria::INACTIVO;
    }

    public function estaEliminado(): bool
    {
        return $this->estado === EstadosAuditoria::ELIMINADO;
    }

    protected function casts(): array
    {
        return [
            /*
             * No se coloca el cast "hashed" aquí porque el historial
             * normalmente recibe un hash ya generado desde el usuario.
             */
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }
}
