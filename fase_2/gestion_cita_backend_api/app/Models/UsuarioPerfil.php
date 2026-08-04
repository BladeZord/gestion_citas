<?php

namespace App\Models;

use App\Model\Constants\EstadosAuditoria;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'usuario_id',
    'perfil_id',
    'estado',
    'usuario_creacion',
    'usuario_actualizacion',
])]
class UsuarioPerfil extends Pivot
{
    protected $table = 'usuario_perfil';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public const CREATED_AT = 'fecha_creacion';

    public const UPDATED_AT = 'fecha_actualizacion';

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id'
        );
    }

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(
            Perfil::class,
            'perfil_id'
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
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }
}
