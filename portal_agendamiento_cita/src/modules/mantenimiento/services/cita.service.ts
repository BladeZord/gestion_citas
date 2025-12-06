import axios from 'axios';
import { environment } from '../../../environment/environment';
import { 
    Cita, 
    CitaRequest, 
    CitaUpdateRequest, 
    CitaEstadoRequest,
    CitasFiltros,
    CitasPaginadas,
    CitaResponse,
    DashboardStats,
    DashboardResponse
} from '../interfaces/cita.interface';
import { getAuthHeaders } from './api.helper';

class CitaService {
    private readonly apiUrl: string;

    constructor() {
        this.apiUrl = environment.apiCitas;
    }

    /**
     * Obtiene una lista paginada de citas con filtros opcionales
     * @param filtros Filtros opcionales para la búsqueda
     * @returns Promise con las citas paginadas
     */
    async listar(filtros?: CitasFiltros): Promise<CitasPaginadas> {
        try {
            const params = new URLSearchParams();
            
            if (filtros?.fecha_cita) params.append('fecha_cita', filtros.fecha_cita);
            if (filtros?.user_id) params.append('user_id', filtros.user_id.toString());
            if (filtros?.tipo_cita_id) params.append('tipo_cita_id', filtros.tipo_cita_id.toString());
            if (filtros?.estado) params.append('estado', filtros.estado);

            const queryString = params.toString();
            const url = queryString ? `${this.apiUrl}?${queryString}` : this.apiUrl;

            const response = await axios.get<CitaResponse>(
                url,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as CitasPaginadas;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al obtener las citas.'
                );
            }
            throw new Error('Error desconocido al obtener las citas');
        }
    }

    /**
     * Obtiene una cita por su ID
     * @param id ID de la cita
     * @returns Promise con la cita
     */
    async obtenerPorId(id: number): Promise<Cita> {
        try {
            const response = await axios.get<CitaResponse>(
                `${this.apiUrl}/${id}`,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Cita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al obtener la cita.'
                );
            }
            throw new Error('Error desconocido al obtener la cita');
        }
    }

    /**
     * Crea una nueva cita
     * @param data Datos de la cita
     * @returns Promise con la cita creada
     */
    async crear(data: CitaRequest): Promise<Cita> {
        try {
            const response = await axios.post<CitaResponse>(
                this.apiUrl,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Cita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al crear la cita.'
                );
            }
            throw new Error('Error desconocido al crear la cita');
        }
    }

    /**
     * Actualiza una cita existente
     * @param id ID de la cita
     * @param data Datos a actualizar
     * @returns Promise con la cita actualizada
     */
    async actualizar(id: number, data: CitaUpdateRequest): Promise<Cita> {
        try {
            const response = await axios.put<CitaResponse>(
                `${this.apiUrl}/${id}`,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Cita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al actualizar la cita.'
                );
            }
            throw new Error('Error desconocido al actualizar la cita');
        }
    }

    /**
     * Actualiza solo el estado de una cita
     * @param id ID de la cita
     * @param estado Nuevo estado
     * @returns Promise con la cita actualizada
     */
    async actualizarEstado(id: number, estado: 'pendiente' | 'confirmada' | 'cancelada'): Promise<Cita> {
        try {
            const data: CitaEstadoRequest = { estado };
            const response = await axios.put<CitaResponse>(
                `${this.apiUrl}/${id}/estado`,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Cita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al actualizar el estado de la cita.'
                );
            }
            throw new Error('Error desconocido al actualizar el estado de la cita');
        }
    }

    /**
     * Elimina una cita
     * @param id ID de la cita
     * @returns Promise<void>
     */
    async eliminar(id: number): Promise<void> {
        try {
            await axios.delete(
                `${this.apiUrl}/${id}`,
                { headers: getAuthHeaders() }
            );
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al eliminar la cita.'
                );
            }
            throw new Error('Error desconocido al eliminar la cita');
        }
    }

    /**
     * Obtiene estadísticas del dashboard
     * @returns Promise con las estadísticas del dashboard
     */
    async obtenerDashboard(): Promise<DashboardStats> {
        try {
            const response = await axios.get<DashboardResponse>(
                `${this.apiUrl}/dashboard`,
                { headers: getAuthHeaders() }
            );
            return response.data.datos;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al obtener las estadísticas del dashboard.'
                );
            }
            throw new Error('Error desconocido al obtener las estadísticas del dashboard');
        }
    }
}

export const citaService = new CitaService();

