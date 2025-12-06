<?php

namespace App\Services\Contract;

use App\Models\Cita;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CitaServiceInterface
{
    public function listarCitas(array $filtros = []): LengthAwarePaginator;
    public function obtenerCita(int $id): ?Cita;
    public function listarCitasPorUsuario(int $userId, array $filtros = []): LengthAwarePaginator;
    public function crearCita(array $data): Cita;
    public function actualizarCita(int $id, array $data): Cita;
    public function actualizarEstadoCita(int $id, string $estado): Cita;
    public function eliminarCita(int $id): bool;
    public function obtenerDashboard(): array;
}
