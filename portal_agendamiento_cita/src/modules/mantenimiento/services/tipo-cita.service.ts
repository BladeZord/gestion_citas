import axios from 'axios';
import { environment } from '../../../environment/environment';
import { TipoCita, TipoCitaRequest, TipoCitaResponse } from '../interfaces/tipo-cita.interface';
import { getAuthHeaders } from './api.helper';

class TipoCitaService {
    private readonly apiUrl: string;

    constructor() {
        this.apiUrl = environment.apiTiposCita;
    }

    /**
     * Obtiene todos los tipos de cita
     * @returns Promise con la lista de tipos de cita
     */
    async listar(): Promise<TipoCita[]> {
        try {
            const response = await axios.get<TipoCitaResponse>(
                this.apiUrl,
                { headers: getAuthHeaders() }
            );
            return Array.isArray(response.data.datos) 
                ? response.data.datos 
                : [response.data.datos];
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al obtener los tipos de cita.'
                );
            }
            throw new Error('Error desconocido al obtener los tipos de cita');
        }
    }

    /**
     * Crea un nuevo tipo de cita
     * @param data Datos del tipo de cita
     * @returns Promise con el tipo de cita creado
     */
    async crear(data: TipoCitaRequest): Promise<TipoCita> {
        try {
            const response = await axios.post<TipoCitaResponse>(
                this.apiUrl,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as TipoCita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al crear el tipo de cita.'
                );
            }
            throw new Error('Error desconocido al crear el tipo de cita');
        }
    }

    /**
     * Actualiza un tipo de cita existente
     * @param id ID del tipo de cita
     * @param data Datos a actualizar
     * @returns Promise con el tipo de cita actualizado
     */
    async actualizar(id: number, data: Partial<TipoCitaRequest>): Promise<TipoCita> {
        try {
            const response = await axios.put<TipoCitaResponse>(
                `${this.apiUrl}/${id}`,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as TipoCita;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al actualizar el tipo de cita.'
                );
            }
            throw new Error('Error desconocido al actualizar el tipo de cita');
        }
    }

    /**
     * Elimina un tipo de cita
     * @param id ID del tipo de cita
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
                    'Error al eliminar el tipo de cita.'
                );
            }
            throw new Error('Error desconocido al eliminar el tipo de cita');
        }
    }
}

export const tipoCitaService = new TipoCitaService();

