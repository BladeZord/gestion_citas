// Interfaces para Tipos de Cita

export interface TipoCita {
    id: number;
    nombre: string;
    created_at?: string;
    updated_at?: string;
}

export interface TipoCitaRequest {
    nombre: string;
}

export interface TipoCitaResponse {
    codigo: number;
    descripcion: string;
    mensaje: string;
    datos: TipoCita | TipoCita[];
}

