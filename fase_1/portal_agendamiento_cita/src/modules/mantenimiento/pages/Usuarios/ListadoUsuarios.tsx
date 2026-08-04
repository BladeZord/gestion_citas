import React, { useEffect, useState } from 'react';
import { Table, Button, Space, Typography, Popconfirm, message } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import { TableColumnsType } from 'antd';
import { usuarioService } from '../../services/usuario.service';
import { Usuario } from '../../interfaces/usuario.interface';
import CrearUsuarioModal from './CrearUsuarioModal';
import EditarUsuarioModal from './EditarUsuarioModal';

const { Title } = Typography;

export default function ListadoUsuarios(): React.ReactElement {
    const [usuarios, setUsuarios] = useState<Usuario[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [modalCrearOpen, setModalCrearOpen] = useState<boolean>(false);
    const [modalEditarOpen, setModalEditarOpen] = useState<boolean>(false);
    const [usuarioIdEditar, setUsuarioIdEditar] = useState<number | null>(null);

    useEffect(() => {
        cargarUsuarios();
    }, []);

    const cargarUsuarios = async (): Promise<void> => {
        setLoading(true);
        try {
            const datos = await usuarioService.listar();
            setUsuarios(datos);
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar los usuarios';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    const handleEliminar = async (id: number): Promise<void> => {
        try {
            await usuarioService.eliminar(id);
            message.success('Usuario eliminado exitosamente');
            cargarUsuarios();
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al eliminar el usuario';
            message.error(errorMessage);
        }
    };

    const getRolTag = (rol: string) => {
        const colores: { [key: string]: string } = {
            admin: 'red',
            usuario: 'blue',
        };
        return <span style={{ 
            padding: '4px 8px', 
            borderRadius: '4px', 
            backgroundColor: colores[rol] || '#d9d9d9',
            color: '#fff',
            fontSize: '12px'
        }}>{rol.toUpperCase()}</span>;
    };

    const columns: TableColumnsType<Usuario> = [
        {
            title: 'ID',
            dataIndex: 'id',
            key: 'id',
            width: 80,
        },
        {
            title: 'Nombre',
            dataIndex: 'nombre',
            key: 'nombre',
        },
        {
            title: 'Correo',
            dataIndex: 'correo',
            key: 'correo',
        },
        {
            title: 'Rol',
            dataIndex: 'rol',
            key: 'rol',
            render: (rol) => getRolTag(rol),
        },
        {
            title: 'Acciones',
            key: 'acciones',
            width: 200,
            render: (_, record) => (
                <Space>
                    <Button
                        type="primary"
                        icon={<EditOutlined />}
                        size="small"
                        onClick={() => {
                            setUsuarioIdEditar(record.id);
                            setModalEditarOpen(true);
                        }}
                    >
                        Editar
                    </Button>
                    <Popconfirm
                        title="¿Estás seguro de eliminar este usuario?"
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
                <Title level={3}>Usuarios</Title>
                <Button
                    type="primary"
                    icon={<PlusOutlined />}
                    onClick={() => setModalCrearOpen(true)}
                >
                    Nuevo Usuario
                </Button>
            </div>

            <Table
                columns={columns}
                dataSource={usuarios}
                rowKey="id"
                loading={loading}
                pagination={{ pageSize: 10 }}
            />

            <CrearUsuarioModal
                open={modalCrearOpen}
                onClose={() => setModalCrearOpen(false)}
                onSuccess={() => {
                    cargarUsuarios();
                }}
            />

            <EditarUsuarioModal
                open={modalEditarOpen}
                usuarioId={usuarioIdEditar}
                onClose={() => {
                    setModalEditarOpen(false);
                    setUsuarioIdEditar(null);
                }}
                onSuccess={() => {
                    cargarUsuarios();
                }}
            />
        </div>
    );
}

