// Interfaces para Citas
import { TipoCita } from './tipo-cita.interface';
import { Usuario } from './usuario.interface';

export interface Cita {
    id: number;
    user_id: number;
    tipo_cita_id: number;
    fecha_cita: string;
    hora_cita: string;
    estado: 'pendiente' | 'confirmada' | 'cancelada';
    created_at?: string;
    updated_at?: string;
    usuario?: Usuario;
    tipo_cita?: TipoCita;
}

export interface CitaRequest {
    user_id: number;
    tipo_cita_id: number;
    fecha_cita: string;
    hora_cita: string;
    estado?: 'pendiente' | 'confirmada' | 'cancelada';
}

export interface CitaUpdateRequest {
    user_id?: number;
    tipo_cita_id?: number;
    fecha_cita?: string;
    hora_cita?: string;
    estado?: 'pendiente' | 'confirmada' | 'cancelada';
}

export interface CitaEstadoRequest {
    estado: 'pendiente' | 'confirmada' | 'cancelada';
}

export interface CitasFiltros {
    fecha_cita?: string;
    user_id?: number;
    tipo_cita_id?: number;
    estado?: 'pendiente' | 'confirmada' | 'cancelada';
}

export interface CitasPaginadas {
    current_page: number;
    data: Cita[];
    per_page: number;
    total: number;
}

export interface CitaResponse {
    codigo: number;
    descripcion: string;
    mensaje: string;
    datos: Cita | CitasPaginadas;
}

export interface DashboardStats {
    total_citas: number;
    citas_por_estado: {
        pendiente: number;
        confirmada: number;
        cancelada: number;
    };
    citas_hoy: number;
    citas_hoy_por_estado: {
        pendiente: number;
        confirmada: number;
        cancelada: number;
    };
    citas_semana: number;
    citas_semana_por_estado: {
        pendiente: number;
        confirmada: number;
        cancelada: number;
    };
    citas_mes: number;
    citas_proximas: number;
    citas_vencidas: number;
}

export interface DashboardResponse {
    codigo: number;
    descripcion: string;
    mensaje: string;
    datos: DashboardStats;
}

