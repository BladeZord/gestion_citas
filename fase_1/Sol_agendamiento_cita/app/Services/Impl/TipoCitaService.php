<?php

namespace App\Services\Impl;

use App\Repository\Contract\TipoCitaRepositoryInterface;
use App\Services\Contract\TipoCitaServiceInterface;
use App\Models\TipoCita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TipoCitaService implements TipoCitaServiceInterface
{
    protected TipoCitaRepositoryInterface $tipoCitaRepository;

    public function __construct(TipoCitaRepositoryInterface $tipoCitaRepository)
    {
        $this->tipoCitaRepository = $tipoCitaRepository;
    }

    public function listarTiposCita(): Collection
    {
        Log::info('Service: Listando tipos de cita');

        $tiposCita = $this->tipoCitaRepository->findAll();

        Log::info('Service: Tipos de cita listados exitosamente', ['total' => $tiposCita->count()]);

        return $tiposCita;
    }

    public function obtenerTipoCita(int $id): ?TipoCita
    {
        Log::info('Service: Obteniendo tipo de cita', ['tipo_cita_id' => $id]);

        $tipoCita = $this->tipoCitaRepository->findById($id);

        if (!$tipoCita) {
            Log::warning('Service: Tipo de cita no encontrado', ['tipo_cita_id' => $id]);
            throw new \Exception('Tipo de cita no encontrado', 404);
        }

        Log::info('Service: Tipo de cita obtenido exitosamente', ['tipo_cita_id' => $id]);

        return $tipoCita;
    }

    public function crearTipoCita(array $data): TipoCita
    {
        Log::info('Service: Creando nuevo tipo de cita', ['data' => $data]);

        $tipoCita = $this->tipoCitaRepository->create($data);

        Log::info('Service: Tipo de cita creado exitosamente', ['tipo_cita_id' => $tipoCita->id]);

        return $tipoCita;
    }

    public function actualizarTipoCita(int $id, array $data): TipoCita
    {
        Log::info('Service: Actualizando tipo de cita', ['tipo_cita_id' => $id, 'data' => $data]);

        $tipoCita = $this->tipoCitaRepository->findById($id);
        if (!$tipoCita) {
            Log::warning('Service: Tipo de cita no encontrado para actualizar', ['tipo_cita_id' => $id]);
            throw new \Exception('Tipo de cita no encontrado', 404);
        }

        $this->tipoCitaRepository->update($id, $data);
        $tipoCita = $this->tipoCitaRepository->findById($id);

        Log::info('Service: Tipo de cita actualizado exitosamente', ['tipo_cita_id' => $id]);

        return $tipoCita;
    }

    public function eliminarTipoCita(int $id): bool
    {
        Log::info('Service: Eliminando tipo de cita', ['tipo_cita_id' => $id]);

        $tipoCita = $this->tipoCitaRepository->findById($id);
        if (!$tipoCita) {
            Log::warning('Service: Tipo de cita no encontrado para eliminar', ['tipo_cita_id' => $id]);
            throw new \Exception('Tipo de cita no encontrado', 404);
        }

        if ($this->tipoCitaRepository->hasCitas($id)) {
            Log::warning('Service: No se puede eliminar tipo de cita con citas asociadas', ['tipo_cita_id' => $id]);
            throw new \Exception('No se puede eliminar el tipo de cita porque tiene citas asociadas', 400);
        }

        $result = $this->tipoCitaRepository->delete($id);

        Log::info('Service: Tipo de cita eliminado exitosamente', ['tipo_cita_id' => $id]);

        return $result;
    }
}
