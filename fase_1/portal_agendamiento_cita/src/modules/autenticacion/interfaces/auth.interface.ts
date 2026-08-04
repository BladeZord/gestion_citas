// Interfaces para autenticación

export interface LoginRequest {
    correo: string;
    password: string;
}

export interface Usuario {
    id: number;
    nombre: string;
    correo: string;
    rol: string;
}

export interface LoginResponseData {
    token: string;
    usuario: Usuario;
}

export interface LoginResponse {
    codigo: number;
    descripcion: string;
    mensaje: string;
    datos: LoginResponseData;
}

