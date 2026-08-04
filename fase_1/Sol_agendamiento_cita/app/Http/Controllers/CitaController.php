<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Requests\UpdateEstadoCitaRequest;
use App\Services\Contract\CitaServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CitaController extends Controller
{
    protected CitaServiceInterface $citaService;

    public function __construct(CitaServiceInterface $citaService)
    {
        $this->citaService = $citaService;
    }
    /**
     * Listar citas por usuario
     */
    public function citasPorUsuario(Request $request, $userId)
    {
        Log::info('Controller: Listado de citas por usuario solicitado', [
            'user_id' => $userId,
            'filtros' => $request->only(['fecha_cita', 'tipo_cita_id', 'estado'])
        ]);

        try {
            $filtros = $request->only(['fecha_cita', 'tipo_cita_id', 'estado']);
            $citas = $this->citaService->listarCitasPorUsuario((int)$userId, $filtros);

            Log::info('Controller: Citas por usuario obtenidas exitosamente', [
                'user_id' => $userId,
                'total' => $citas->total()
            ]);

            return ApiResponse::success($citas, 'Citas del usuario obtenidas exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al listar citas por usuario', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Listar citas con paginación y filtros
     */
    public function index(Request $request)
    {
        Log::info('Controller: Listado de citas solicitado', [
            'filtros' => $request->only(['fecha_inicio', 'fecha_fin', 'user_id', 'tipo_cita_id', 'estado'])
        ]);

        try {
            $filtros = $request->only(['fecha_inicio', 'fecha_fin', 'user_id', 'tipo_cita_id', 'estado']);
            $citas = $this->citaService->listarCitas($filtros);

            Log::info('Controller: Citas obtenidas exitosamente', ['total' => $citas->total()]);

            return ApiResponse::success($citas, 'Citas obtenidas exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al listar citas', ['error' => $e->getMessage()]);
            return ApiResponse::error('Error al obtener las citas', 500);
        }
    }

    /**
     * Crear una nueva cita
     */
    public function store(StoreCitaRequest $request)
    {
        Log::info('Controller: Creación de cita iniciada', [
            'user_id' => $request->user_id,
            'tipo_cita_id' => $request->tipo_cita_id,
            'fecha_cita' => $request->fecha_cita,
            'hora_cita' => $request->hora_cita
        ]);

        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? 'pendiente';

            $cita = $this->citaService->crearCita($data);

            Log::info('Controller: Cita creada exitosamente', ['cita_id' => $cita->id]);

            return ApiResponse::success($cita, 'Cita creada exitosamente', 201);
        } catch (\Exception $e) {
            Log::error('Controller: Error al crear cita', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Obtener detalle de una cita
     */
    public function show($id)
    {
        Log::info('Controller: Consulta de cita solicitada', ['cita_id' => $id]);

        try {
            $cita = $this->citaService->obtenerCita($id);

            Log::info('Controller: Cita obtenida exitosamente', ['cita_id' => $id]);

            return ApiResponse::success($cita, 'Cita obtenida exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al obtener cita', [
                'cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Actualizar una cita
     */
    public function update(UpdateCitaRequest $request, $id)
    {
        Log::info('Controller: Actualización de cita iniciada', [
            'cita_id' => $id,
            'campos' => array_keys($request->validated())
        ]);

        try {
            $cita = $this->citaService->actualizarCita($id, $request->validated());

            Log::info('Controller: Cita actualizada exitosamente', ['cita_id' => $id]);

            return ApiResponse::success($cita, 'Cita actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al actualizar cita', [
                'cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Actualizar solo el estado de una cita
     */
    public function updateEstado(UpdateEstadoCitaRequest $request, $id)
    {
        Log::info('Controller: Actualización de estado de cita iniciada', [
            'cita_id' => $id,
            'nuevo_estado' => $request->estado
        ]);

        try {
            $cita = $this->citaService->actualizarEstadoCita($id, $request->estado);

            Log::info('Controller: Estado de cita actualizado exitosamente', [
                'cita_id' => $id,
                'estado_nuevo' => $request->estado
            ]);

            return ApiResponse::success($cita, 'Estado de cita actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al actualizar estado de cita', [
                'cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Eliminar una cita
     */
    public function destroy($id)
    {
        Log::info('Controller: Eliminación de cita solicitada', ['cita_id' => $id]);

        try {
            $this->citaService->eliminarCita($id);

            Log::info('Controller: Cita eliminada exitosamente', ['cita_id' => $id]);

            return ApiResponse::success(null, 'Cita eliminada exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al eliminar cita', [
                'cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Obtener estadísticas de dashboard
     */
    public function dashboard()
    {
        Log::info('Controller: Estadísticas de dashboard solicitadas');

        try {
            $estadisticas = $this->citaService->obtenerDashboard();

            Log::info('Controller: Estadísticas de dashboard obtenidas exitosamente');

            return ApiResponse::success($estadisticas, 'Estadísticas de dashboard obtenidas exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al obtener estadísticas de dashboard', [
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

