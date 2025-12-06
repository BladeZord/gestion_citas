<?php

namespace App\Repository\Impl;

use App\Models\Cita;
use App\Repository\Contract\CitaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CitaRepository implements CitaRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        Log::info('Repository: Buscando citas con filtros', ['filtros' => $filters, 'per_page' => $perPage]);
        
        $query = Cita::with(['usuario', 'tipoCita']);

        if (isset($filters['fecha_cita'])) {
            $query->where('fecha_cita', $filters['fecha_cita']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['tipo_cita_id'])) {
            $query->where('tipo_cita_id', $filters['tipo_cita_id']);
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        $citas = $query->paginate($perPage);
        
        Log::info('Repository: Citas obtenidas', ['total' => $citas->total()]);
        
        return $citas;
    }

    public function findById(int $id): ?Cita
    {
        Log::info('Repository: Buscando cita por ID', ['cita_id' => $id]);
        
        $cita = Cita::with(['usuario', 'tipoCita'])->find($id);
        
        if ($cita) {
            Log::info('Repository: Cita encontrada', ['cita_id' => $id]);
        } else {
            Log::warning('Repository: Cita no encontrada', ['cita_id' => $id]);
        }
        
        return $cita;
    }

    public function create(array $data): Cita
    {
        Log::info('Repository: Creando nueva cita', ['data' => $data]);
        
        $cita = Cita::create($data);
        $cita->load(['usuario', 'tipoCita']);
        
        Log::info('Repository: Cita creada exitosamente', ['cita_id' => $cita->id]);
        
        return $cita;
    }

    public function update(int $id, array $data): bool
    {
        Log::info('Repository: Actualizando cita', ['cita_id' => $id, 'data' => $data]);
        
        $result = Cita::where('id', $id)->update($data);
        
        if ($result) {
            Log::info('Repository: Cita actualizada exitosamente', ['cita_id' => $id]);
        } else {
            Log::warning('Repository: No se pudo actualizar la cita', ['cita_id' => $id]);
        }
        
        return $result > 0;
    }

    public function delete(int $id): bool
    {
        Log::info('Repository: Eliminando cita', ['cita_id' => $id]);
        
        $result = Cita::where('id', $id)->delete();
        
        if ($result) {
            Log::info('Repository: Cita eliminada exitosamente', ['cita_id' => $id]);
        } else {
            Log::warning('Repository: No se pudo eliminar la cita', ['cita_id' => $id]);
        }
        
        return $result > 0;
    }

    public function existsByUserFechaHora(int $userId, string $fechaCita, string $horaCita, ?int $excludeId = null): bool
    {
        Log::info('Repository: Verificando existencia de cita', [
            'user_id' => $userId,
            'fecha_cita' => $fechaCita,
            'hora_cita' => $horaCita,
            'exclude_id' => $excludeId
        ]);
        
        $query = Cita::where('user_id', $userId)
            ->where('fecha_cita', $fechaCita)
            ->where('hora_cita', $horaCita);
        
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        
        $exists = $query->exists();
        
        Log::info('Repository: Verificación de cita completada', ['existe' => $exists]);
        
        return $exists;
    }

    public function obtenerEstadisticasDashboard(): array
    {
        Log::info('Repository: Obteniendo estadísticas de dashboard');
        
        // Total de citas
        $totalCitas = Cita::count();
        
        // Citas agrupadas por estado
        $citasPorEstado = Cita::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
        
        // Asegurar que todos los estados estén presentes con 0 si no existen
        $estados = ['pendiente', 'confirmada', 'cancelada'];
        $citasPorEstadoCompleto = [];
        foreach ($estados as $estado) {
            $citasPorEstadoCompleto[$estado] = $citasPorEstado[$estado] ?? 0;
        }
        
        // Citas del día actual
        $citasHoy = Cita::whereDate('fecha_cita', today())->count();
        
        // Citas de la semana actual
        $citasSemana = Cita::whereBetween('fecha_cita', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        // Citas del mes actual
        $citasMes = Cita::whereMonth('fecha_cita', now()->month)
            ->whereYear('fecha_cita', now()->year)
            ->count();
        
        // Citas próximas (próximos 7 días)
        $citasProximas = Cita::whereBetween('fecha_cita', [
            today(),
            now()->addDays(7)
        ])->count();
        
        // Citas vencidas (fechas pasadas con estado pendiente o confirmada)
        $citasVencidas = Cita::whereDate('fecha_cita', '<', today())
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->count();
        
        // Citas por estado del día actual
        $citasHoyPorEstado = Cita::whereDate('fecha_cita', today())
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
        
        $citasHoyPorEstadoCompleto = [];
        foreach ($estados as $estado) {
            $citasHoyPorEstadoCompleto[$estado] = $citasHoyPorEstado[$estado] ?? 0;
        }
        
        // Citas por estado de la semana actual
        $citasSemanaPorEstado = Cita::whereBetween('fecha_cita', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();
        
        $citasSemanaPorEstadoCompleto = [];
        foreach ($estados as $estado) {
            $citasSemanaPorEstadoCompleto[$estado] = $citasSemanaPorEstado[$estado] ?? 0;
        }
        
        $estadisticas = [
            'total_citas' => $totalCitas,
            'citas_por_estado' => $citasPorEstadoCompleto,
            'citas_hoy' => $citasHoy,
            'citas_hoy_por_estado' => $citasHoyPorEstadoCompleto,
            'citas_semana' => $citasSemana,
            'citas_semana_por_estado' => $citasSemanaPorEstadoCompleto,
            'citas_mes' => $citasMes,
            'citas_proximas' => $citasProximas,
            'citas_vencidas' => $citasVencidas,
        ];
        
        Log::info('Repository: Estadísticas de dashboard obtenidas exitosamente', $estadisticas);
        
        return $estadisticas;
    }
}
