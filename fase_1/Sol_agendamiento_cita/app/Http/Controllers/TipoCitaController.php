<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreTipoCitaRequest;
use App\Http\Requests\UpdateTipoCitaRequest;
use App\Services\Contract\TipoCitaServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TipoCitaController extends Controller
{
    protected TipoCitaServiceInterface $tipoCitaService;

    public function __construct(TipoCitaServiceInterface $tipoCitaService)
    {
        $this->tipoCitaService = $tipoCitaService;
    }
    /**
     * Listar todos los tipos de cita
     */
    public function index()
    {
        Log::info('Controller: Listado de tipos de cita solicitado');

        try {
            $tiposCita = $this->tipoCitaService->listarTiposCita();

            Log::info('Controller: Tipos de cita obtenidos exitosamente', ['total' => $tiposCita->count()]);

            return ApiResponse::success($tiposCita, 'Tipos de cita obtenidos exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al listar tipos de cita', ['error' => $e->getMessage()]);
            return ApiResponse::error('Error al obtener los tipos de cita', 500);
        }
    }

    /**
     * Crear un nuevo tipo de cita
     */
    public function store(StoreTipoCitaRequest $request)
    {
        Log::info('Controller: Creación de tipo de cita iniciada', ['nombre' => $request->nombre]);

        try {
            $tipoCita = $this->tipoCitaService->crearTipoCita($request->validated());

            Log::info('Controller: Tipo de cita creado exitosamente', ['tipo_cita_id' => $tipoCita->id]);

            return ApiResponse::success($tipoCita, 'Tipo de cita creado exitosamente', 201);
        } catch (\Exception $e) {
            Log::error('Controller: Error al crear tipo de cita', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Actualizar un tipo de cita
     */
    public function update(UpdateTipoCitaRequest $request, $id)
    {
        Log::info('Controller: Actualización de tipo de cita iniciada', [
            'tipo_cita_id' => $id,
            'campos' => array_keys($request->validated())
        ]);

        try {
            $tipoCita = $this->tipoCitaService->actualizarTipoCita($id, $request->validated());

            Log::info('Controller: Tipo de cita actualizado exitosamente', ['tipo_cita_id' => $id]);

            return ApiResponse::success($tipoCita, 'Tipo de cita actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al actualizar tipo de cita', [
                'tipo_cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Eliminar un tipo de cita
     */
    public function destroy($id)
    {
        Log::info('Controller: Eliminación de tipo de cita solicitada', ['tipo_cita_id' => $id]);

        try {
            $this->tipoCitaService->eliminarTipoCita($id);

            Log::info('Controller: Tipo de cita eliminado exitosamente', ['tipo_cita_id' => $id]);

            return ApiResponse::success(null, 'Tipo de cita eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al eliminar tipo de cita', [
                'tipo_cita_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

