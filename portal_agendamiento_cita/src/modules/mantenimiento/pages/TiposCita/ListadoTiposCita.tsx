import React, { useEffect, useState } from 'react';
import { Table, Button, Space, Typography, Popconfirm, message, Modal, Form, Input } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import { TableColumnsType } from 'antd';
import { tipoCitaService } from '../../services/tipo-cita.service';
import { TipoCita, TipoCitaRequest } from '../../interfaces/tipo-cita.interface';

const { Title } = Typography;

export default function ListadoTiposCita(): React.ReactElement {
    const [tiposCita, setTiposCita] = useState<TipoCita[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [modalVisible, setModalVisible] = useState<boolean>(false);
    const [editingRecord, setEditingRecord] = useState<TipoCita | null>(null);
    const [form] = Form.useForm<TipoCitaRequest>();

    useEffect(() => {
        cargarTiposCita();
    }, []);

    const cargarTiposCita = async (): Promise<void> => {
        setLoading(true);
        try {
            const datos = await tipoCitaService.listar();
            setTiposCita(datos);
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar los tipos de cita';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    const handleCrear = (): void => {
        setEditingRecord(null);
        form.resetFields();
        setModalVisible(true);
    };

    const handleEditar = (record: TipoCita): void => {
        setEditingRecord(record);
        form.setFieldsValue({ nombre: record.nombre });
        setModalVisible(true);
    };

    const handleEliminar = async (id: number): Promise<void> => {
        try {
            await tipoCitaService.eliminar(id);
            message.success('Tipo de cita eliminado exitosamente');
            cargarTiposCita();
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al eliminar el tipo de cita';
            message.error(errorMessage);
        }
    };

    const handleSubmit = async (values: TipoCitaRequest): Promise<void> => {
        try {
            if (editingRecord) {
                await tipoCitaService.actualizar(editingRecord.id, values);
                message.success('Tipo de cita actualizado exitosamente');
            } else {
                await tipoCitaService.crear(values);
                message.success('Tipo de cita creado exitosamente');
            }
            setModalVisible(false);
            form.resetFields();
            cargarTiposCita();
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al guardar el tipo de cita';
            message.error(errorMessage);
        }
    };

    const columns: TableColumnsType<TipoCita> = [
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
            title: 'Acciones',
            key: 'acciones',
            width: 150,
            render: (_, record) => (
                <Space>
                    <Button
                        type="primary"
                        icon={<EditOutlined />}
                        size="small"
                        onClick={() => handleEditar(record)}
                    >
                        Editar
                    </Button>
                    <Popconfirm
                        title="¿Estás seguro de eliminar este tipo de cita?"
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
                <Title level={3}>Tipos de Cita</Title>
                <Button
                    type="primary"
                    icon={<PlusOutlined />}
                    onClick={handleCrear}
                >
                    Nuevo Tipo de Cita
                </Button>
            </div>

            <Table
                columns={columns}
                dataSource={tiposCita}
                rowKey="id"
                loading={loading}
                pagination={{ pageSize: 10 }}
            />

            <Modal
                title={editingRecord ? 'Editar Tipo de Cita' : 'Nuevo Tipo de Cita'}
                open={modalVisible}
                onCancel={() => {
                    setModalVisible(false);
                    form.resetFields();
                }}
                footer={null}
            >
                <Form
                    form={form}
                    layout="vertical"
                    onFinish={handleSubmit}
                >
                    <Form.Item
                        name="nombre"
                        label="Nombre"
                        rules={[
                            { required: true, message: 'Por favor ingresa el nombre del tipo de cita' }
                        ]}
                    >
                        <Input placeholder="Ej: Consulta General" />
                    </Form.Item>

                    <Form.Item style={{ marginBottom: 0, textAlign: 'right' }}>
                        <Space>
                            <Button onClick={() => {
                                setModalVisible(false);
                                form.resetFields();
                            }}>
                                Cancelar
                            </Button>
                            <Button type="primary" htmlType="submit">
                                {editingRecord ? 'Actualizar' : 'Crear'}
                            </Button>
                        </Space>
                    </Form.Item>
                </Form>
            </Modal>
        </div>
    );
}

