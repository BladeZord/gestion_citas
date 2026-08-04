import React, { useEffect, useState } from 'react';
import { Form, Input, Button, Space, Typography, message, DatePicker, TimePicker, Select } from 'antd';
import { Link } from 'react-router-dom';
import dayjs, { Dayjs } from 'dayjs';
import { citaService } from '../../services/cita.service';
import { tipoCitaService } from '../../services/tipo-cita.service';
import { usuarioService } from '../../services/usuario.service';
import { authService } from '../../../autenticacion/services/auth.service';
import { CitaRequest } from '../../interfaces/cita.interface';
import { TipoCita } from '../../interfaces/tipo-cita.interface';
import { Usuario } from '../../interfaces/usuario.interface';

const { Title } = Typography;

interface FormValues {
    user_id: number;
    tipo_cita_id: number;
    fecha_cita: Dayjs;
    hora_cita: Dayjs;
    estado?: 'pendiente' | 'confirmada' | 'cancelada';
}

export default function CrearCita(): React.ReactElement {
    const [form] = Form.useForm<FormValues>();
    const [loading, setLoading] = useState<boolean>(false);
    const [tiposCita, setTiposCita] = useState<TipoCita[]>([]);
    const [loadingTipos, setLoadingTipos] = useState<boolean>(false);
    const [usuarios, setUsuarios] = useState<Usuario[]>([]);
    const [loadingUsuarios, setLoadingUsuarios] = useState<boolean>(false);
    
    // Obtener usuario actual
    const usuarioActual = authService.getUsuario();
    const esAdmin = usuarioActual?.rol === 'admin';
    const esUsuario = usuarioActual?.rol === 'usuario';

    useEffect(() => {
        cargarTiposCita();
        if (esAdmin) {
            cargarUsuarios();
        } else if (esUsuario && usuarioActual?.id) {
            // Si es usuario, setear automáticamente su ID
            form.setFieldsValue({ user_id: usuarioActual.id });
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

    const onSubmit = async (values: any): Promise<void> => {
        setLoading(true);
        try {
            const data: CitaRequest = {
                user_id: values.user_id,
                tipo_cita_id: values.tipo_cita_id,
                fecha_cita: values.fecha_cita.format('YYYY-MM-DD'),
                hora_cita: values.hora_cita.format('HH:mm'),
                estado: values.estado || 'pendiente',
            };

            await citaService.crear(data);
            message.success('Cita creada exitosamente');
            form.resetFields();
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al crear la cita';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            <div style={{ textAlign: 'center', marginBottom: '30px' }}>
                <Title level={3}>Crear Nueva Cita</Title>
            </div>
            <Form<FormValues>
                form={form}
                layout="vertical"
                onFinish={onSubmit}
                style={{ maxWidth: '600px', margin: '0 auto' }}
            >
                {esUsuario ? (
                    <>
                        <Form.Item
                            label="Usuario"
                        >
                            <Input 
                                value={usuarioActual?.nombre || ''} 
                                disabled 
                                readOnly
                            />
                        </Form.Item>
                        <Form.Item
                            name="user_id"
                            hidden
                            initialValue={usuarioActual?.id}
                        >
                            <Input type="hidden" />
                        </Form.Item>
                    </>
                ) : (
                    <Form.Item
                        label="Usuario"
                        name="user_id"
                        rules={[{ required: true, message: 'Por favor selecciona un usuario' }]}
                    >
                        <Select
                            placeholder="Selecciona un usuario"
                            loading={loadingUsuarios}
                        >
                            {usuarios.map((usuario) => (
                                <Select.Option key={usuario.id} value={usuario.id}>
                                    {usuario.nombre} ({usuario.correo})
                                </Select.Option>
                            ))}
                        </Select>
                    </Form.Item>
                )}

                <Form.Item
                    label="Tipo de Cita"
                    name="tipo_cita_id"
                    rules={[{ required: true, message: 'Por favor selecciona un tipo de cita' }]}
                >
                    <Select
                        placeholder="Selecciona un tipo de cita"
                        loading={loadingTipos}
                    >
                        {tiposCita.map((tipo) => (
                            <Select.Option key={tipo.id} value={tipo.id}>
                                {tipo.nombre}
                            </Select.Option>
                        ))}
                    </Select>
                </Form.Item>

                <Form.Item
                    label="Fecha de la Cita"
                    name="fecha_cita"
                    rules={[{ required: true, message: 'Por favor selecciona la fecha' }]}
                >
                    <DatePicker
                        style={{ width: '100%' }}
                        format="YYYY-MM-DD"
                        disabledDate={(current) => current && current < dayjs().startOf('day')}
                    />
                </Form.Item>

                <Form.Item
                    label="Hora de la Cita"
                    name="hora_cita"
                    rules={[{ required: true, message: 'Por favor selecciona la hora' }]}
                >
                    <TimePicker
                        style={{ width: '100%' }}
                        format="HH:mm"
                    />
                </Form.Item>

                <Form.Item
                    label="Estado"
                    name="estado"
                    initialValue="pendiente"
                >
                    <Select>
                        <Select.Option value="pendiente">Pendiente</Select.Option>
                        <Select.Option value="confirmada">Confirmada</Select.Option>
                        <Select.Option value="cancelada">Cancelada</Select.Option>
                    </Select>
                </Form.Item>

                <Form.Item style={{ textAlign: 'center', marginTop: '24px' }}>
                    <Space>
                        <Button type="primary" htmlType="submit" loading={loading}>
                            Crear Cita
                        </Button>
                        <Link to="/citas">
                            <Button danger>Cancelar</Button>
                        </Link>
                    </Space>
                </Form.Item>
            </Form>
        </div>
    );
}

