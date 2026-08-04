<?php

namespace App\Models;

use App\Models\Constants\EstadosAuditoria;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'codigo',
    'nombre',
    'estado',
    'usuario_creacion',
    'usuario_actualizacion',
])]
class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'fecha_creacion';

    public const UPDATED_AT = 'fecha_actualizacion';

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class, 'categoria_id');
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_creacion');
    }

    public function usuarioActualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion');
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
