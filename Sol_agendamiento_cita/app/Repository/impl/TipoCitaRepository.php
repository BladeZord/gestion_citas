<?php

namespace App\Repository\Impl;

use App\Models\TipoCita;
use App\Repository\Contract\TipoCitaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TipoCitaRepository implements TipoCitaRepositoryInterface
{
    public function findAll(): Collection
    {
        Log::info('Repository: Buscando todos los tipos de cita');
        
        $tiposCita = TipoCita::all();
        
        Log::info('Repository: Tipos de cita obtenidos', ['total' => $tiposCita->count()]);
        
        return $tiposCita;
    }

    public function findById(int $id): ?TipoCita
    {
        Log::info('Repository: Buscando tipo de cita por ID', ['tipo_cita_id' => $id]);
        
        $tipoCita = TipoCita::find($id);
        
        if ($tipoCita) {
            Log::info('Repository: Tipo de cita encontrado', ['tipo_cita_id' => $id]);
        } else {
            Log::warning('Repository: Tipo de cita no encontrado', ['tipo_cita_id' => $id]);
        }
        
        return $tipoCita;
    }

    public function create(array $data): TipoCita
    {
        Log::info('Repository: Creando nuevo tipo de cita', ['data' => $data]);
        
        $tipoCita = TipoCita::create($data);
        
        Log::info('Repository: Tipo de cita creado exitosamente', ['tipo_cita_id' => $tipoCita->id]);
        
        return $tipoCita;
    }

    public function update(int $id, array $data): bool
    {
        Log::info('Repository: Actualizando tipo de cita', ['tipo_cita_id' => $id, 'data' => $data]);
        
        $result = TipoCita::where('id', $id)->update($data);
        
        if ($result) {
            Log::info('Repository: Tipo de cita actualizado exitosamente', ['tipo_cita_id' => $id]);
        } else {
            Log::warning('Repository: No se pudo actualizar el tipo de cita', ['tipo_cita_id' => $id]);
        }
        
        return $result > 0;
    }

    public function delete(int $id): bool
    {
        Log::info('Repository: Eliminando tipo de cita', ['tipo_cita_id' => $id]);
        
        $result = TipoCita::where('id', $id)->delete();
        
        if ($result) {
            Log::info('Repository: Tipo de cita eliminado exitosamente', ['tipo_cita_id' => $id]);
        } else {
            Log::warning('Repository: No se pudo eliminar el tipo de cita', ['tipo_cita_id' => $id]);
        }
        
        return $result > 0;
    }

    public function hasCitas(int $id): bool
    {
        Log::info('Repository: Verificando si tipo de cita tiene citas asociadas', ['tipo_cita_id' => $id]);
        
        $tipoCita = TipoCita::find($id);
        
        if (!$tipoCita) {
            Log::warning('Repository: Tipo de cita no encontrado para verificar citas', ['tipo_cita_id' => $id]);
            return false;
        }
        
        $hasCitas = $tipoCita->citas()->count() > 0;
        
        Log::info('Repository: Verificación de citas completada', [
            'tipo_cita_id' => $id,
            'tiene_citas' => $hasCitas
        ]);
        
        return $hasCitas;
    }
}
