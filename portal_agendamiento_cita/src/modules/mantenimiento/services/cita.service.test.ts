import { citaService } from './cita.service';
import { getAuthHeaders } from './api.helper';
import axios from 'axios';
import { environment } from '../../../environment/environment';

jest.mock('axios');
jest.mock('./api.helper');

const mockedAxios = axios as jest.Mocked<typeof axios>;
const mockedGetAuthHeaders = getAuthHeaders as jest.MockedFunction<typeof getAuthHeaders>;

describe('CitaService', () => {
    beforeEach(() => {
        jest.clearAllMocks();
        mockedGetAuthHeaders.mockReturnValue({
            'Authorization': 'Bearer test-token',
            'Content-Type': 'application/json',
        });
    });

    describe('listar', () => {
        it('debe hacer una petición GET con los filtros correctos', async () => {
            const mockResponse = {
                data: {
                    codigo: 200,
                    descripcion: 'OK',
                    mensaje: 'Citas obtenidas exitosamente',
                    datos: {
                        current_page: 1,
                        data: [],
                        per_page: 15,
                        total: 0,
                    },
                },
            };

            mockedAxios.get.mockResolvedValue(mockResponse);

            const filtros = {
                fecha_cita: '2024-12-01',
                user_id: 2,
                tipo_cita_id: 5,
                estado: 'pendiente' as const,
            };

            await citaService.listar(filtros);

            expect(mockedAxios.get).toHaveBeenCalledTimes(1);
            expect(mockedAxios.get).toHaveBeenCalledWith(
                `${environment.apiCitas}?fecha_cita=2024-12-01&user_id=2&tipo_cita_id=5&estado=pendiente`,
                { headers: mockedGetAuthHeaders() }
            );
        });

        it('debe hacer petición sin query params si no hay filtros', async () => {
            const mockResponse = {
                data: {
                    codigo: 200,
                    descripcion: 'OK',
                    mensaje: 'Citas obtenidas exitosamente',
                    datos: {
                        current_page: 1,
                        data: [],
                        per_page: 15,
                        total: 0,
                    },
                },
            };

            mockedAxios.get.mockResolvedValue(mockResponse);

            await citaService.listar();

            expect(mockedAxios.get).toHaveBeenCalledWith(
                environment.apiCitas,
                { headers: mockedGetAuthHeaders() }
            );
        });
    });

    describe('crear', () => {
        it('debe hacer una petición POST con los datos correctos', async () => {
            const mockResponse = {
                data: {
                    codigo: 201,
                    descripcion: 'Creado exitosamente',
                    mensaje: 'Cita creada exitosamente',
                    datos: {
                        id: 1,
                        user_id: 2,
                        tipo_cita_id: 5,
                        fecha_cita: '2024-12-14',
                        hora_cita: '07:09:00',
                        estado: 'pendiente',
                    },
                },
            };

            mockedAxios.post.mockResolvedValue(mockResponse);

            const nuevaCita = {
                user_id: 2,
                tipo_cita_id: 5,
                fecha_cita: '2024-12-14',
                hora_cita: '07:09',
                estado: 'pendiente' as const,
            };

            await citaService.crear(nuevaCita);

            expect(mockedAxios.post).toHaveBeenCalledTimes(1);
            expect(mockedAxios.post).toHaveBeenCalledWith(
                environment.apiCitas,
                nuevaCita,
                { headers: mockedGetAuthHeaders() }
            );
        });
    });
});

