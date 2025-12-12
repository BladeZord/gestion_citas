import axios from "axios";
import { environment } from "../../../environment/environment";
import {
  LoginRequest,
  LoginResponse,
  Usuario,
} from "../interfaces/auth.interface";

class AuthService {
  private readonly apiUrl: string;

  constructor() {
    this.apiUrl = environment.apiLogin;
  }

  /**
   * Limpia una respuesta que puede contener advertencias HTML antes del JSON
   * @param data Datos de la respuesta (puede ser string o objeto)
   * @returns Objeto parseado limpio
   */
  private cleanResponse(data: any): LoginResponse {
    // Si ya es un objeto, retornarlo directamente
    if (typeof data === 'object' && data !== null) {
      return data as LoginResponse;
    }

    // Si es un string, intentar extraer el JSON
    if (typeof data === 'string') {
      // Buscar el primer { que indica el inicio del JSON
      const jsonStart = data.indexOf('{');
      if (jsonStart !== -1) {
        try {
          const jsonString = data.substring(jsonStart);
          return JSON.parse(jsonString) as LoginResponse;
        } catch (e) {
          console.warn('Error al parsear respuesta limpia:', e);
        }
      }
    }

    // Si no se puede parsear, lanzar error
    throw new Error('Respuesta del servidor en formato inválido');
  }

  /**
   * Realiza el login del usuario
   * @param credentials Credenciales de login (correo y password)
   * @returns Promise con la respuesta del servidor
   */
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    try {
      const response = await axios.post(
        this.apiUrl,
        credentials,
        {
          headers: {
            "Content-Type": "application/json",
          },
          // Configurar transformResponse para manejar respuestas con texto antes del JSON
          transformResponse: [(data) => {
            try {
              // Intentar parsear directamente
              return typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
              // Si falla, intentar limpiar la respuesta
              const jsonStart = data.indexOf('{');
              if (jsonStart !== -1) {
                return JSON.parse(data.substring(jsonStart));
              }
              throw e;
            }
          }],
        }
      );
      
      // Limpiar la respuesta por si acaso
      return this.cleanResponse(response.data);
    } catch (error) {
      if (axios.isAxiosError(error)) {
        // Intentar extraer el mensaje de error de la respuesta
        let errorMessage = "Error al realizar el login. Por favor, intenta nuevamente.";
        
        if (error.response?.data) {
          try {
            const cleanedData = this.cleanResponse(error.response.data);
            errorMessage = cleanedData.mensaje || errorMessage;
          } catch (e) {
            // Si la respuesta tiene un mensaje directo, usarlo
            if (typeof error.response.data === 'object' && error.response.data.mensaje) {
              errorMessage = error.response.data.mensaje;
            } else if (typeof error.response.data === 'string') {
              // Intentar extraer mensaje de un string
              const jsonMatch = error.response.data.match(/"mensaje"\s*:\s*"([^"]+)"/);
              if (jsonMatch) {
                errorMessage = jsonMatch[1];
              }
            }
          }
        }
        
        throw new Error(errorMessage);
      }
      throw new Error("Error desconocido al realizar el login");
    }
  }

  /**
   * Guarda el token en localStorage
   * @param token Token de autenticación
   */
  saveToken(token: string): void {
    localStorage.setItem("authToken", token);
  }

  /**
   * Guardar los datos del usuario en localStorage
   * @param usuario Usuario
   */
  saveUser(usuario: Usuario | null): void {
    if (!usuario) {
      return;
    }

    localStorage.setItem("usuario", JSON.stringify(usuario));
  }

  /**
   * Obtiene el token almacenado
   * @returns Token de autenticación o null si no existe
   */
  getToken(): string | null {
    return localStorage.getItem("authToken");
  }

  /**
   * Obtiene el usuario almacenado
   * @returns Usuario o null si no existe
   */
  getUsuario(): Usuario | null {
    const usuarioStr = localStorage.getItem("usuario");
    if (!usuarioStr) {
      return null;
    }
    try {
      return JSON.parse(usuarioStr) as Usuario;
    } catch {
      return null;
    }
  }

  /**
   * Elimina el token del almacenamiento
   */
  removeToken(): void {
    localStorage.removeItem("authToken");
  }

  /**
   * Verifica si el usuario está autenticado
   * @returns true si existe un token, false en caso contrario
   */
  isAuthenticated(): boolean {
    return this.getToken() !== null;
  }

  /**
   * Cierra la sesión del usuario
   * Elimina el token y todos los datos de autenticación almacenados
   */
  logout(): void {
    // Eliminar token
    this.removeToken();

    // Eliminar datos del usuario
    localStorage.removeItem("usuario");

    // Limpiar cualquier otro dato relacionado con la sesión
    sessionStorage.clear();

    // También limpiar localStorage completo si es necesario
    // localStorage.clear(); // Descomentar si se necesita limpiar todo
  }
}

export const authService = new AuthService();
