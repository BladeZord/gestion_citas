// Interfaces para Usuarios

export interface Usuario {
    id: number;
    nombre: string;
    correo: string;
    rol: string;
    created_at?: string;
    updated_at?: string;
}

export interface UsuarioRequest {
    nombre: string;
    correo: string;
    password: string;
    rol: 'admin' | 'usuario';
}

export interface UsuarioUpdateRequest {
    nombre?: string;
    correo?: string;
    password?: string;
    rol?: 'admin' | 'usuario';
}

export interface UsuarioResponse {
    codigo: number;
    descripcion: string;
    mensaje: string;
    datos: Usuario | Usuario[];
}

