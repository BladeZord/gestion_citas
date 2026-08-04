<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'user_id',
    'ip_address',
    'user_agent',
    'payload',
    'last_activity',
])]
class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    protected function casts(): array
    {
        return [
            'last_activity' => 'integer',
        ];
    }
}
