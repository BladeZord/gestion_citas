import React, { useEffect, useState, useRef } from 'react';
import { Table, Button, Space, Typography, Popconfirm, message, Tag, Select, Input } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined, SearchOutlined } from '@ant-design/icons';
import { TableColumnsType } from 'antd';
import { citaService } from '../../services/cita.service';
import { tipoCitaService } from '../../services/tipo-cita.service';
import { usuarioService } from '../../services/usuario.service';
import { Cita, CitasFiltros } from '../../interfaces/cita.interface';
import { TipoCita } from '../../interfaces/tipo-cita.interface';
import { Usuario } from '../../interfaces/usuario.interface';
import { authService } from '../../../autenticacion/services/auth.service';
import CrearCitaModal from './CrearCitaModal';
import EditarCitaModal from './EditarCitaModal';
import dayjs from 'dayjs';

const { Title } = Typography;

export default function ListadoCitas(): React.ReactElement {
    const [citas, setCitas] = useState<Cita[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [filtrosLocales, setFiltrosLocales] = useState<CitasFiltros>({});
    const [filtrosAplicados, setFiltrosAplicados] = useState<CitasFiltros>({});
    const [modalCrearOpen, setModalCrearOpen] = useState<boolean>(false);
    const [modalEditarOpen, setModalEditarOpen] = useState<boolean>(false);
    const [citaIdEditar, setCitaIdEditar] = useState<number | null>(null);
    const [tiposCita, setTiposCita] = useState<TipoCita[]>([]);
    const [loadingTipos, setLoadingTipos] = useState<boolean>(false);
    const [usuarios, setUsuarios] = useState<Usuario[]>([]);
    const [loadingUsuarios, setLoadingUsuarios] = useState<boolean>(false);
    const cargandoRef = useRef<boolean>(false);
    const inicializadoRef = useRef<boolean>(false);
    
    // Obtener usuario actual
    const usuario = authService.getUsuario();
    const esAdmin = usuario?.rol === 'admin';
    const esUsuario = usuario?.rol === 'usuario';

    // Cargar tipos de cita y usuarios al montar
    useEffect(() => {
        cargarTiposCita();
        if (esAdmin) {
            cargarUsuarios();
        }
    }, [esAdmin]);

    // Inicializar filtro automático para usuarios solo una vez
    useEffect(() => {
        if (!inicializadoRef.current && esUsuario && usuario?.id) {
            inicializadoRef.current = true;
            const filtrosIniciales: CitasFiltros = { user_id: usuario.id };
            setFiltrosLocales(filtrosIniciales);
            setFiltrosAplicados(filtrosIniciales);
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const cargarTiposCita = async (): Promise<void> => {
        setLoadingTipos(true);
        try {
            const datos = await tipoCitaService.listar();
            setTiposCita(datos);
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar los tipos de cita';
            message.error(errorMessage);
        } finally {
            setLoadingTipos(false);
        }
    };

    const cargarUsuarios = async (): Promise<void> => {
        setLoadingUsuarios(true);
        try {
            const datos = await usuarioService.listar();
            setUsuarios(datos);
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar los usuarios';
            message.error(errorMessage);
        } finally {
            setLoadingUsuarios(false);
        }
    };

    // Cargar citas solo cuando cambien los filtros aplicados (no los locales)
    useEffect(() => {
        // Evitar múltiples peticiones simultáneas
        if (cargandoRef.current) {
            return;
        }

        const cargarCitas = async (): Promise<void> => {
            cargandoRef.current = true;
            setLoading(true);
            
            try {
                // Si es usuario, agregar automáticamente su ID a los filtros
                const filtrosAplicar: CitasFiltros = { ...filtrosAplicados };
                if (esUsuario && usuario?.id) {
                    filtrosAplicar.user_id = usuario.id;
                }
                
                const datos = await citaService.listar(filtrosAplicar);
                setCitas(datos.data || []);
            } catch (error) {
                const errorMessage = error instanceof Error 
                    ? error.message 
                    : 'Error al cargar las citas';
                message.error(errorMessage);
            } finally {
                setLoading(false);
                cargandoRef.current = false;
            }
        };

        cargarCitas();
    }, [filtrosAplicados, esUsuario, usuario?.id]);

    const recargarCitas = async (): Promise<void> => {
        // Aplicar los filtros locales actuales
        setFiltrosAplicados({ ...filtrosLocales });
    };

    const handleEliminar = async (id: number): Promise<void> => {
        try {
            await citaService.eliminar(id);
            message.success('Cita eliminada exitosamente');
            recargarCitas();
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al eliminar la cita';
            message.error(errorMessage);
        }
    };

    const handleFiltros = (): void => {
        // Aplicar los filtros locales cuando el usuario presiona "Buscar"
        setFiltrosAplicados({ ...filtrosLocales });
    };

    const handleLimpiarFiltros = (): void => {
        // Si es usuario, mantener el user_id automático
        const nuevosFiltros: CitasFiltros = {};
        if (esUsuario && usuario?.id) {
            nuevosFiltros.user_id = usuario.id;
        }
        setFiltrosLocales(nuevosFiltros);
        setFiltrosAplicados(nuevosFiltros);
    };

    const getEstadoTag = (estado: string) => {
        const estados: { [key: string]: { color: string; text: string } } = {
            pendiente: { color: 'orange', text: 'Pendiente' },
            confirmada: { color: 'green', text: 'Confirmada' },
            cancelada: { color: 'red', text: 'Cancelada' },
        };
        const estadoInfo = estados[estado] || { color: 'default', text: estado };
        return <Tag color={estadoInfo.color}>{estadoInfo.text}</Tag>;
    };

    const columns: TableColumnsType<Cita> = [
        {
            title: 'ID',
            dataIndex: 'id',
            key: 'id',
            width: 80,
        },
        {
            title: 'Usuario',
            key: 'usuario',
            render: (_, record) => record.usuario?.nombre || `ID: ${record.user_id}`,
        },
        {
            title: 'Tipo de Cita',
            key: 'tipo_cita',
            render: (_, record) => record.tipo_cita?.nombre || `ID: ${record.tipo_cita_id}`,
        },
        {
            title: 'Fecha',
            dataIndex: 'fecha_cita',
            key: 'fecha_cita',
            render: (fecha) => dayjs(fecha).format('DD/MM/YYYY'),
        },
        {
            title: 'Hora',
            dataIndex: 'hora_cita',
            key: 'hora_cita',
            render: (hora) => hora ? dayjs(hora, 'HH:mm').format('hh:mm A') : '',
        },
        {
            title: 'Estado',
            dataIndex: 'estado',
            key: 'estado',
            render: (estado) => getEstadoTag(estado),
        },
        {
            title: 'Acciones',
            key: 'acciones',
            width: 300,
            render: (_, record) => (
                <Space>
                    <Button
                        type="primary"
                        icon={<EditOutlined />}
                        size="small"
                        onClick={() => {
                            setCitaIdEditar(record.id);
                            setModalEditarOpen(true);
                        }}
                    >
                        Editar
                    </Button>
                    <Popconfirm
                        title="¿Estás seguro de eliminar esta cita?"
                        onConfirm={() => handleEliminar(record.id)}
                        okText="Sí"
                        cancelText="No"
                    >
                        <Button
                            type="primary"
                            danger
                            icon={<DeleteOutlined />}
                            size="small"
                        >
                            Eliminar
                        </Button>
                    </Popconfirm>
                </Space>
            ),
        },
    ];

    return (
        <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '24px' }}>
                <Title level={3}>Citas</Title>
                <Button
                    type="primary"
                    icon={<PlusOutlined />}
                    onClick={() => setModalCrearOpen(true)}
                >
                    Nueva Cita
                </Button>
            </div>

            <div style={{ marginBottom: '16px', padding: '16px', background: '#f5f5f5', borderRadius: '4px' }}>
                <Space wrap>
                    <Input
                        placeholder="Fecha (YYYY-MM-DD)"
                        value={filtrosLocales.fecha_cita || ''}
                        onChange={(e) => setFiltrosLocales({ ...filtrosLocales, fecha_cita: e.target.value || undefined })}
                        style={{ width: 150 }}
                    />
                    {/* Solo mostrar filtro de usuario si es admin */}
                    {esAdmin && (
                        <Select
                            placeholder="Usuario"
                            value={filtrosLocales.user_id}
                            onChange={(value) => setFiltrosLocales({ ...filtrosLocales, user_id: value || undefined })}
                            style={{ width: 200 }}
                            allowClear
                            loading={loadingUsuarios}
                        >
                            {usuarios.map((usuario) => (
                                <Select.Option key={usuario.id} value={usuario.id}>
                                    {usuario.nombre} ({usuario.correo})
                                </Select.Option>
                            ))}
                        </Select>
                    )}
                    <Select
                        placeholder="Tipo de Cita"
                        value={filtrosLocales.tipo_cita_id}
                        onChange={(value) => setFiltrosLocales({ ...filtrosLocales, tipo_cita_id: value || undefined })}
                        style={{ width: 200 }}
                        allowClear
                        loading={loadingTipos}
                    >
                        {tiposCita.map((tipo) => (
                            <Select.Option key={tipo.id} value={tipo.id}>
                                {tipo.nombre}
                            </Select.Option>
                        ))}
                    </Select>
                    <Select
                        placeholder="Estado"
                        value={filtrosLocales.estado}
                        onChange={(value) => setFiltrosLocales({ ...filtrosLocales, estado: value })}
                        style={{ width: 150 }}
                        allowClear
                    >
                        <Select.Option value="pendiente">Pendiente</Select.Option>
                        <Select.Option value="confirmada">Confirmada</Select.Option>
                        <Select.Option value="cancelada">Cancelada</Select.Option>
                    </Select>
                    <Button
                        type="primary"
                        icon={<SearchOutlined />}
                        onClick={handleFiltros}
                    >
                        Buscar
                    </Button>
                    <Button onClick={handleLimpiarFiltros}>
                        Limpiar
                    </Button>
                </Space>
            </div>

            <Table
                columns={columns}
                dataSource={citas}
                rowKey="id"
                loading={loading}
                pagination={{ pageSize: 10 }}
            />

            <CrearCitaModal
                open={modalCrearOpen}
                onClose={() => setModalCrearOpen(false)}
                onSuccess={() => {
                    recargarCitas();
                }}
            />

            <EditarCitaModal
                open={modalEditarOpen}
                citaId={citaIdEditar}
                onClose={() => {
                    setModalEditarOpen(false);
                    setCitaIdEditar(null);
                }}
                onSuccess={() => {
                    recargarCitas();
                }}
            />
        </div>
    );
}

