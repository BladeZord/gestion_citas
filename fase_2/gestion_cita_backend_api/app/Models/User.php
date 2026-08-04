<?php

namespace App\Models;

use App\Model\Constants\EstadosAuditoria;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'usuario',
    'nombre',
    'apellido',
    'correo',
    'contrasenia',
    'estado',
    'usuario_creacion',
    'usuario_actualizacion',
])]
#[Hidden([
    'contrasenia',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'fecha_creacion';

    public const UPDATED_AT = 'fecha_actualizacion';

    public function getAuthPasswordName(): string
    {
        return 'contrasenia';
    }

    public function getAuthPassword(): string
    {
        return $this->contrasenia;
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'usuario_creacion'
        );
    }

    public function usuarioActualizador(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'usuario_actualizacion'
        );
    }

    public function perfiles(): BelongsToMany
    {
        return $this->belongsToMany(
            Perfil::class,
            'usuario_perfil',
            'usuario_id',
            'perfil_id'
        )
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

    public function historialContrasenias(): HasMany
    {
        return $this->hasMany(
            HistorialContrasenia::class,
            'usuario_id'
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
            'contrasenia' => 'hashed',
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }
}
