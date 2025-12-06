<?php

namespace App\Services\Impl;

use App\Models\Cita;
use App\Repository\Contract\CitaRepositoryInterface;
use App\Repository\Contract\TipoCitaRepositoryInterface;
use App\Repository\Contract\UsuarioRepositoryInterface;
use App\Services\Contract\CitaServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CitaService implements CitaServiceInterface
{
    protected CitaRepositoryInterface $citaRepository;
    protected UsuarioRepositoryInterface $usuarioRepository;
    protected TipoCitaRepositoryInterface $tipoCitaRepository;

    public function __construct(
        CitaRepositoryInterface $citaRepository,
        UsuarioRepositoryInterface $usuarioRepository,
        TipoCitaRepositoryInterface $tipoCitaRepository
    ) {
        $this->citaRepository = $citaRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->tipoCitaRepository = $tipoCitaRepository;
    }

    public function listarCitas(array $filtros = []): LengthAwarePaginator
    {
        Log::info('Service: Listando citas', ['filtros' => $filtros]);

        $citas = $this->citaRepository->findAll($filtros);

        Log::info('Service: Citas listadas exitosamente', ['total' => $citas->total()]);

        return $citas;
    }

    public function obtenerCita(int $id): ?Cita
    {
        Log::info('Service: Obteniendo cita', ['cita_id' => $id]);

        $cita = $this->citaRepository->findById($id);

        if (!$cita) {
            Log::warning('Service: Cita no encontrada', ['cita_id' => $id]);
            throw new \Exception('Cita no encontrada', 404);
        }

        Log::info('Service: Cita obtenida exitosamente', ['cita_id' => $id]);

        return $cita;
    }

    public function listarCitasPorUsuario(int $userId, array $filtros = []): LengthAwarePaginator
    {
        Log::info('Service: Listando citas por usuario', [
            'user_id' => $userId,
            'filtros' => $filtros
        ]);

        // Validar que el usuario existe
        $usuario = $this->usuarioRepository->findById($userId);
        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado para listar citas', ['user_id' => $userId]);
            throw new \Exception('Usuario no encontrado', 404);
        }

        // Agregar user_id a los filtros
        $filtros['user_id'] = $userId;

        $citas = $this->citaRepository->findAll($filtros);

        Log::info('Service: Citas por usuario listadas exitosamente', [
            'user_id' => $userId,
            'total' => $citas->total()
        ]);

        return $citas;
    }

    public function crearCita(array $data): Cita
    {
        Log::info('Service: Creando nueva cita', ['data' => $data]);

        // Validar existencia de usuario
        $usuario = $this->usuarioRepository->findById($data['user_id']);
        if (!$usuario) {
            Log::warning('Service: Usuario no existe para crear cita', ['user_id' => $data['user_id']]);
            throw new \Exception('El usuario no existe', 404);
        }

        // Validar existencia de tipo de cita
        $tipoCita = $this->tipoCitaRepository->findById($data['tipo_cita_id']);
        if (!$tipoCita) {
            Log::warning('Service: Tipo de cita no existe para crear cita', ['tipo_cita_id' => $data['tipo_cita_id']]);
            throw new \Exception('El tipo de cita no existe', 404);
        }

        // Validar que no exista otra cita con misma fecha y hora
        if ($this->citaRepository->existsByUserFechaHora(
            $data['user_id'],
            $data['fecha_cita'],
            $data['hora_cita']
        )) {
            Log::warning('Service: Cita duplicada detectada', [
                'user_id' => $data['user_id'],
                'fecha_cita' => $data['fecha_cita'],
                'hora_cita' => $data['hora_cita']
            ]);
            throw new \Exception('El usuario ya tiene una cita en esa fecha y hora', 422);
        }

        $cita = $this->citaRepository->create($data);

        Log::info('Service: Cita creada exitosamente', ['cita_id' => $cita->id]);

        return $cita;
    }

    public function actualizarCita(int $id, array $data): Cita
    {
        Log::info('Service: Actualizando cita', ['cita_id' => $id, 'data' => $data]);

        $cita = $this->citaRepository->findById($id);
        if (!$cita) {
            Log::warning('Service: Cita no encontrada para actualizar', ['cita_id' => $id]);
            throw new \Exception('Cita no encontrada', 404);
        }

        // Validar existencia de usuario si se proporciona
        if (isset($data['user_id'])) {
            $usuario = $this->usuarioRepository->findById($data['user_id']);
            if (!$usuario) {
                Log::warning('Service: Usuario no existe para actualizar cita', [
                    'cita_id' => $id,
                    'user_id' => $data['user_id']
                ]);
                throw new \Exception('El usuario no existe', 404);
            }
        }

        // Validar existencia de tipo de cita si se proporciona
        if (isset($data['tipo_cita_id'])) {
            $tipoCita = $this->tipoCitaRepository->findById($data['tipo_cita_id']);
            if (!$tipoCita) {
                Log::warning('Service: Tipo de cita no existe para actualizar cita', [
                    'cita_id' => $id,
                    'tipo_cita_id' => $data['tipo_cita_id']
                ]);
                throw new \Exception('El tipo de cita no existe', 404);
            }
        }

        // Validar que no exista otra cita con misma fecha y hora
        if (isset($data['fecha_cita']) || isset($data['hora_cita']) || isset($data['user_id'])) {
            $userId = $data['user_id'] ?? $cita->user_id;
            $fechaCita = $data['fecha_cita'] ?? $cita->fecha_cita;
            $horaCita = $data['hora_cita'] ?? $cita->hora_cita;

            if ($this->citaRepository->existsByUserFechaHora($userId, $fechaCita, $horaCita, $id)) {
                Log::warning('Service: Cita duplicada detectada al actualizar', [
                    'cita_id' => $id,
                    'user_id' => $userId,
                    'fecha_cita' => $fechaCita,
                    'hora_cita' => $horaCita
                ]);
                throw new \Exception('El usuario ya tiene una cita en esa fecha y hora', 422);
            }
        }

        $this->citaRepository->update($id, $data);
        $cita = $this->citaRepository->findById($id);

        Log::info('Service: Cita actualizada exitosamente', ['cita_id' => $id]);

        return $cita;
    }

    public function actualizarEstadoCita(int $id, string $estado): Cita
    {
        Log::info('Service: Actualizando estado de cita', ['cita_id' => $id, 'nuevo_estado' => $estado]);

        $cita = $this->citaRepository->findById($id);
        if (!$cita) {
            Log::warning('Service: Cita no encontrada para actualizar estado', ['cita_id' => $id]);
            throw new \Exception('Cita no encontrada', 404);
        }

        $estadoAnterior = $cita->estado;
        $this->citaRepository->update($id, ['estado' => $estado]);
        $cita = $this->citaRepository->findById($id);

        Log::info('Service: Estado de cita actualizado exitosamente', [
            'cita_id' => $id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estado
        ]);

        return $cita;
    }

    public function eliminarCita(int $id): bool
    {
        Log::info('Service: Eliminando cita', ['cita_id' => $id]);

        $cita = $this->citaRepository->findById($id);
        if (!$cita) {
            Log::warning('Service: Cita no encontrada para eliminar', ['cita_id' => $id]);
            throw new \Exception('Cita no encontrada', 404);
        }

        $result = $this->citaRepository->delete($id);

        Log::info('Service: Cita eliminada exitosamente', ['cita_id' => $id]);

        return $result;
    }

    public function obtenerDashboard(): array
    {
        Log::info('Service: Obteniendo estadísticas de dashboard');

        $estadisticas = $this->citaRepository->obtenerEstadisticasDashboard();

        Log::info('Service: Estadísticas de dashboard obtenidas exitosamente', $estadisticas);

        return $estadisticas;
    }
}
