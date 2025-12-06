<?php

namespace App\Services\Contract;

use App\Models\TipoCita;
use Illuminate\Database\Eloquent\Collection;

interface TipoCitaServiceInterface
{
    public function listarTiposCita(): Collection;
    public function obtenerTipoCita(int $id): ?TipoCita;
    public function crearTipoCita(array $data): TipoCita;
    public function actualizarTipoCita(int $id, array $data): TipoCita;
    public function eliminarTipoCita(int $id): bool;
}
