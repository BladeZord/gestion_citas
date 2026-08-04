<?php

namespace App\Models;

use App\Model\Constants\EstadosAuditoria;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'codigo',
    'descripcion',
    'estado',
    'usuario_creacion',
    'usuario_actualizacion',
])]
class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfiles';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'fecha_creacion';

    public const UPDATED_AT = 'fecha_actualizacion';

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'usuario_perfil',
            'perfil_id',
            'usuario_id'
        )
            ->using(UsuarioPerfil::class)
            ->withPivot([
                'id',
                'estado',
                'fecha_creacion',
                'usuario_creacion',
                'fecha_actualizacion',
                'usuario_actualizacion',
            ])
            ->wherePivot('estado', EstadosAuditoria::ACTIVO);
    }

    public function asignacionesUsuarios(): HasMany
    {
        return $this->hasMany(
            UsuarioPerfil::class,
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
