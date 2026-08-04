import React from 'react';
import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import ListadoCitas from './ListadoCitas';
import { citaService } from '../../services/cita.service';
import { authService } from '../../../autenticacion/services/auth.service';
import * as antd from 'antd';

// Mock de los servicios
jest.mock('../../services/cita.service');
jest.mock('../../../autenticacion/services/auth.service');
jest.mock('antd', () => {
    const actual = jest.requireActual('antd');
    return {
        ...actual,
        message: {
            success: jest.fn(),
            error: jest.fn(),
            warning: jest.fn(),
            info: jest.fn(),
        },
    };
});

const mockCitaService = citaService as jest.Mocked<typeof citaService>;
const mockAuthService = authService as jest.Mocked<typeof authService>;

// Helper para renderizar con router
const renderWithRouter = (component: React.ReactElement) => {
    return render(<BrowserRouter>{component}</BrowserRouter>);
};

describe('ListadoCitas', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        jest.clearAllTimers();
        jest.useFakeTimers();
    });

    afterEach(() => {
        jest.runOnlyPendingTimers();
        jest.useRealTimers();
    });

    describe('Comportamiento de peticiones', () => {
        it('debe hacer solo una petición al montar el componente', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            });
        });

        it('NO debe hacer peticiones al escribir en los campos de filtro', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            // Esperar a que se complete la carga inicial
            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            }, { timeout: 3000 });

            // Limpiar el contador de llamadas
            (mockCitaService.listar as jest.Mock).mockClear();

            // Escribir en el campo de fecha
            const fechaInput = screen.getByPlaceholderText('Fecha (YYYY-MM-DD)');
            fireEvent.change(fechaInput, { target: { value: '2024-12-01' } });

            // Avanzar el tiempo significativamente
            jest.advanceTimersByTime(5000);

            // No debe hacer peticiones adicionales solo por escribir
            expect(mockCitaService.listar).not.toHaveBeenCalled();
        });

        it('debe hacer petición solo cuando se presiona el botón Buscar', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            // Esperar carga inicial
            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            }, { timeout: 3000 });

            // Limpiar contador
            (mockCitaService.listar as jest.Mock).mockClear();

            // Escribir en filtros (no debe hacer petición)
            const fechaInput = screen.getByPlaceholderText('Fecha (YYYY-MM-DD)');
            fireEvent.change(fechaInput, { target: { value: '2024-12-01' } });

            // Verificar que NO se hizo petición al escribir
            expect(mockCitaService.listar).not.toHaveBeenCalled();

            // Presionar botón Buscar (ahora SÍ debe hacer petición)
            const buscarButton = screen.getByText('Buscar');
            fireEvent.click(buscarButton);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            }, { timeout: 3000 });
        });

        it('NO debe hacer múltiples peticiones simultáneas', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            // Simular una petición lenta
            let resolvePromise: (value: any) => void;
            const slowPromise = new Promise((resolve) => {
                resolvePromise = resolve;
            });

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockReturnValue(slowPromise as any);

            renderWithRouter(<ListadoCitas />);

            // Avanzar el tiempo para que se inicie la primera petición
            jest.advanceTimersByTime(100);

            // Intentar hacer otra petición mientras la primera está en curso
            const buscarButton = screen.getByText('Buscar');
            fireEvent.click(buscarButton);
            fireEvent.click(buscarButton);
            fireEvent.click(buscarButton);

            // Resolver la primera petición
            resolvePromise!(mockCitas);
            await waitFor(() => {
                // Solo debe haberse llamado una vez (la primera)
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            });
        });
    });

    describe('Comportamiento según rol', () => {
        it('debe agregar automáticamente user_id cuando el usuario es "usuario"', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 2,
                nombre: 'Usuario Test',
                correo: 'usuario@test.com',
                rol: 'usuario',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledWith(
                    expect.objectContaining({
                        user_id: 2,
                    })
                );
            });
        });

        it('NO debe mostrar el filtro de usuario cuando el rol es "usuario"', () => {
            mockAuthService.getUsuario.mockReturnValue({
                id: 2,
                nombre: 'Usuario Test',
                correo: 'usuario@test.com',
                rol: 'usuario',
            });

            mockCitaService.listar.mockResolvedValue({
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            });

            renderWithRouter(<ListadoCitas />);

            const usuarioInput = screen.queryByPlaceholderText('ID Usuario');
            expect(usuarioInput).not.toBeInTheDocument();
        });

        it('debe mostrar el filtro de usuario cuando el rol es "admin"', () => {
            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue({
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            });

            renderWithRouter(<ListadoCitas />);

            const usuarioInput = screen.getByPlaceholderText('ID Usuario');
            expect(usuarioInput).toBeInTheDocument();
        });
    });

    describe('Funcionalidad de filtros', () => {
        it('debe aplicar todos los filtros al buscar', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            });

            jest.clearAllMocks();

            // Llenar filtros
            const fechaInicioInput = screen.getByPlaceholderText('Fecha Inicio');
            const fechaFinInput = screen.getByPlaceholderText('Fecha Fin');
            const tipoCitaInput = screen.getByPlaceholderText('ID Tipo Cita');
            const estadoSelect = screen.getByPlaceholderText('Estado');

            fireEvent.change(fechaInicioInput, { target: { value: '2024-12-01' } });
            fireEvent.change(fechaFinInput, { target: { value: '2024-12-31' } });
            fireEvent.change(tipoCitaInput, { target: { value: '5' } });
            fireEvent.change(estadoSelect, { target: { value: 'pendiente' } });

            // Presionar buscar
            const buscarButton = screen.getByText('Buscar');
            fireEvent.click(buscarButton);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledWith(
                    expect.objectContaining({
                        fecha_inicio: '2024-12-01',
                        fecha_fin: '2024-12-31',
                        tipo_cita_id: 5,
                        estado: 'pendiente',
                    })
                );
            });
        });

        it('debe limpiar los filtros correctamente', async () => {
            const mockCitas = {
                current_page: 1,
                data: [],
                per_page: 15,
                total: 0,
            };

            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockResolvedValue(mockCitas);

            renderWithRouter(<ListadoCitas />);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledTimes(1);
            });

            // Llenar filtros
            const fechaInput = screen.getByPlaceholderText('Fecha (YYYY-MM-DD)');
            fireEvent.change(fechaInput, { target: { value: '2024-12-01' } });

            jest.clearAllMocks();

            // Limpiar filtros
            const limpiarButton = screen.getByText('Limpiar');
            fireEvent.click(limpiarButton);

            await waitFor(() => {
                expect(mockCitaService.listar).toHaveBeenCalledWith({});
            });
        });
    });

    describe('Manejo de errores', () => {
        it('debe mostrar mensaje de error cuando falla la petición', async () => {
            mockAuthService.getUsuario.mockReturnValue({
                id: 1,
                nombre: 'Admin',
                correo: 'admin@test.com',
                rol: 'admin',
            });

            mockCitaService.listar.mockRejectedValue(new Error('Error de red'));

            renderWithRouter(<ListadoCitas />);

            await waitFor(() => {
                expect(antd.message.error).toHaveBeenCalledWith('Error de red');
            });
        });
    });
});

