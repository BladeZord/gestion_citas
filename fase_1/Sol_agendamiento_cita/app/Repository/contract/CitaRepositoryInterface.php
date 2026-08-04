<?php

namespace App\Repository\Contract;

use App\Models\Cita;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CitaRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Cita;
    public function create(array $data): Cita;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function existsByUserFechaHora(int $userId, string $fechaCita, string $horaCita, ?int $excludeId = null): bool;
    public function obtenerEstadisticasDashboard(): array;
}
