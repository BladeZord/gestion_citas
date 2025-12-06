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
   * Realiza el login del usuario
   * @param credentials Credenciales de login (correo y password)
   * @returns Promise con la respuesta del servidor
   */
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    try {
      const response = await axios.post<LoginResponse>(
        this.apiUrl,
        credentials,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );
      return response.data;
    } catch (error) {
      if (axios.isAxiosError(error)) {
        throw new Error(
          error.response?.data?.mensaje ||
            "Error al realizar el login. Por favor, intenta nuevamente."
        );
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
