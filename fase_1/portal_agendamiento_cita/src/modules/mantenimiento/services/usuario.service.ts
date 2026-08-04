import axios from 'axios';
import { environment } from '../../../environment/environment';
import { Usuario, UsuarioRequest, UsuarioUpdateRequest, UsuarioResponse } from '../interfaces/usuario.interface';
import { getAuthHeaders } from './api.helper';

class UsuarioService {
    private readonly apiUrl: string;

    constructor() {
        this.apiUrl = environment.apiUsuarios;
    }

    /**
     * Obtiene todos los usuarios
     * @returns Promise con la lista de usuarios
     */
    async listar(): Promise<Usuario[]> {
        try {
            const response = await axios.get<UsuarioResponse>(
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
                    'Error al obtener los usuarios.'
                );
            }
            throw new Error('Error desconocido al obtener los usuarios');
        }
    }

    /**
     * Obtiene un usuario por su ID
     * @param id ID del usuario
     * @returns Promise con el usuario
     */
    async obtenerPorId(id: number): Promise<Usuario> {
        try {
            const response = await axios.get<UsuarioResponse>(
                `${this.apiUrl}/${id}`,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Usuario;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al obtener el usuario.'
                );
            }
            throw new Error('Error desconocido al obtener el usuario');
        }
    }

    /**
     * Crea un nuevo usuario
     * @param data Datos del usuario
     * @returns Promise con el usuario creado
     */
    async crear(data: UsuarioRequest): Promise<Usuario> {
        try {
            const response = await axios.post<UsuarioResponse>(
                this.apiUrl,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Usuario;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al crear el usuario.'
                );
            }
            throw new Error('Error desconocido al crear el usuario');
        }
    }

    /**
     * Actualiza un usuario existente
     * @param id ID del usuario
     * @param data Datos a actualizar
     * @returns Promise con el usuario actualizado
     */
    async actualizar(id: number, data: UsuarioUpdateRequest): Promise<Usuario> {
        try {
            const response = await axios.put<UsuarioResponse>(
                `${this.apiUrl}/${id}`,
                data,
                { headers: getAuthHeaders() }
            );
            return response.data.datos as Usuario;
        } catch (error) {
            if (axios.isAxiosError(error)) {
                throw new Error(
                    error.response?.data?.mensaje || 
                    'Error al actualizar el usuario.'
                );
            }
            throw new Error('Error desconocido al actualizar el usuario');
        }
    }

    /**
     * Elimina un usuario
     * @param id ID del usuario
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
                    'Error al eliminar el usuario.'
                );
            }
            throw new Error('Error desconocido al eliminar el usuario');
        }
    }
}

export const usuarioService = new UsuarioService();

