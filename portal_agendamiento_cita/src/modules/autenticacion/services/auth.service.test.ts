import { authService } from './auth.service';
import axios from 'axios';
import { environment } from '../../../environment/environment';

jest.mock('axios');
jest.mock('../../../environment/environment', () => ({
    environment: {
        apiLogin: 'http://127.0.0.1:8000/api/login/',
    },
}));

const mockedAxios = axios as jest.Mocked<typeof axios>;

// Mock de localStorage
const localStorageMock = (() => {
    let store: { [key: string]: string } = {};

    return {
        getItem: (key: string) => store[key] || null,
        setItem: (key: string, value: string) => {
            store[key] = value.toString();
        },
        removeItem: (key: string) => {
            delete store[key];
        },
        clear: () => {
            store = {};
        },
    };
})();

Object.defineProperty(window, 'localStorage', {
    value: localStorageMock,
});

describe('AuthService', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        localStorageMock.clear();
    });

    describe('login', () => {
        it('debe hacer una petición POST con las credenciales correctas', async () => {
            const mockResponse = {
                data: {
                    codigo: 200,
                    descripcion: 'OK',
                    mensaje: 'Login exitoso',
                    datos: {
                        token: 'test-token-123',
                        usuario: {
                            id: 2,
                            nombre: 'Usuario Test',
                            correo: 'usuario@example.com',
                            rol: 'usuario',
                        },
                    },
                },
            };

            mockedAxios.post.mockResolvedValue(mockResponse);

            const credentials = {
                correo: 'usuario@example.com',
                password: 'password123',
            };

            const result = await authService.login(credentials);

            expect(mockedAxios.post).toHaveBeenCalledTimes(1);
            expect(mockedAxios.post).toHaveBeenCalledWith(
                environment.apiLogin,
                credentials,
                {
                    headers: {
                        'Content-Type': 'application/json',
                    },
                }
            );
            expect(result).toEqual(mockResponse.data);
        });

        it('debe lanzar error cuando las credenciales son incorrectas', async () => {
            const mockError = {
                response: {
                    data: {
                        mensaje: 'Credenciales incorrectas',
                    },
                },
                isAxiosError: true,
            };

            mockedAxios.post.mockRejectedValue(mockError);
            mockedAxios.isAxiosError.mockReturnValue(true);

            const credentials = {
                correo: 'usuario@example.com',
                password: 'wrong-password',
            };

            await expect(authService.login(credentials)).rejects.toThrow(
                'Credenciales incorrectas'
            );
        });
    });

    describe('saveToken y getToken', () => {
        it('debe guardar y recuperar el token correctamente', () => {
            const token = 'test-token-123';

            authService.saveToken(token);
            const retrievedToken = authService.getToken();

            expect(retrievedToken).toBe(token);
        });
    });

    describe('saveUser y getUsuario', () => {
        it('debe guardar y recuperar el usuario correctamente', () => {
            const usuario = {
                id: 2,
                nombre: 'Usuario Test',
                correo: 'usuario@example.com',
                rol: 'usuario',
            };

            authService.saveUser(usuario);
            const retrievedUsuario = authService.getUsuario();

            expect(retrievedUsuario).toEqual(usuario);
        });
    });

    describe('logout', () => {
        it('debe eliminar el token y el usuario del localStorage', () => {
            authService.saveToken('test-token');
            authService.saveUser({
                id: 2,
                nombre: 'Usuario Test',
                correo: 'usuario@example.com',
                rol: 'usuario',
            });

            authService.logout();

            expect(authService.getToken()).toBeNull();
            expect(authService.getUsuario()).toBeNull();
        });
    });

    describe('isAuthenticated', () => {
        it('debe retornar true cuando hay un token', () => {
            authService.saveToken('test-token');
            expect(authService.isAuthenticated()).toBe(true);
        });

        it('debe retornar false cuando no hay token', () => {
            localStorageMock.clear();
            expect(authService.isAuthenticated()).toBe(false);
        });
    });
});

