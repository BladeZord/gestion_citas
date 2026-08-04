import React, { useEffect, useState, useCallback } from 'react';
import { Form, Input, Button, Modal, message, DatePicker, TimePicker, Select } from 'antd';
import dayjs, { Dayjs } from 'dayjs';
import { citaService } from '../../services/cita.service';
import { tipoCitaService } from '../../services/tipo-cita.service';
import { usuarioService } from '../../services/usuario.service';
import { authService } from '../../../autenticacion/services/auth.service';
import { CitaUpdateRequest, Cita } from '../../interfaces/cita.interface';
import { TipoCita } from '../../interfaces/tipo-cita.interface';
import { Usuario } from '../../interfaces/usuario.interface';

interface FormValues {
    user_id?: number;
    tipo_cita_id?: number;
    fecha_cita?: Dayjs;
    hora_cita?: Dayjs;
    estado?: 'pendiente' | 'confirmada' | 'cancelada';
}

interface EditarCitaModalProps {
    open: boolean;
    citaId: number | null;
    onClose: () => void;
    onSuccess: () => void;
}

export default function EditarCitaModal({ open, citaId, onClose, onSuccess }: EditarCitaModalProps): React.ReactElement {
    const [form] = Form.useForm<FormValues>();
    const [loading, setLoading] = useState<boolean>(false);
    const [cargando, setCargando] = useState<boolean>(false);
    const [tiposCita, setTiposCita] = useState<TipoCita[]>([]);
    const [loadingTipos, setLoadingTipos] = useState<boolean>(false);
    const [usuarios, setUsuarios] = useState<Usuario[]>([]);
    const [loadingUsuarios, setLoadingUsuarios] = useState<boolean>(false);
    const [cita, setCita] = useState<Cita | null>(null);
    
    // Obtener usuario actual
    const usuarioActual = authService.getUsuario();
    const esAdmin = usuarioActual?.rol === 'admin';
    const esUsuario = usuarioActual?.rol === 'usuario';

    useEffect(() => {
        if (open && citaId) {
            cargarCita();
            cargarTiposCita();
            if (esAdmin) {
                cargarUsuarios();
            }
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open, citaId]);

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

    const cargarCita = useCallback(async (): Promise<void> => {
        if (!citaId) return;
        
        setCargando(true);
        try {
            const citaData = await citaService.obtenerPorId(citaId);
            setCita(citaData);
            
            // Si es usuario, verificar que la cita pertenece a él
            if (esUsuario && usuarioActual?.id && citaData.user_id !== usuarioActual.id) {
                message.error('No tienes permiso para editar esta cita');
                onClose();
                return;
            }
            
            form.setFieldsValue({
                user_id: citaData.user_id,
                tipo_cita_id: citaData.tipo_cita_id,
                fecha_cita: dayjs(citaData.fecha_cita),
                hora_cita: dayjs(citaData.hora_cita, 'HH:mm:ss'),
                estado: citaData.estado,
            });
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar la cita';
            message.error(errorMessage);
            onClose();
        } finally {
            setCargando(false);
        }
    }, [citaId, form, esUsuario, usuarioActual, onClose]);

    const handleSubmit = async (): Promise<void> => {
        if (!citaId) return;

        try {
            const values = await form.validateFields();
            setLoading(true);
            
            const data: CitaUpdateRequest = {
                user_id: values.user_id,
                tipo_cita_id: values.tipo_cita_id,
                fecha_cita: values.fecha_cita?.format('YYYY-MM-DD'),
                hora_cita: values.hora_cita?.format('HH:mm'),
                estado: values.estado,
            };

            await citaService.actualizar(citaId, data);
            message.success('Cita actualizada exitosamente');
            form.resetFields();
            onSuccess();
            onClose();
        } catch (error: any) {
            if (error?.errorFields) {
                // Errores de validación del formulario
                return;
            }
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al actualizar la cita';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = (): void => {
        form.resetFields();
        setCita(null);
        onClose();
    };

    return (
        <Modal
            title="Editar Cita"
            open={open}
            onCancel={handleCancel}
            onOk={handleSubmit}
            confirmLoading={loading || cargando}
            width={600}
            okText="Actualizar"
            cancelText="Cancelar"
        >
            {cargando ? (
                <div style={{ textAlign: 'center', padding: '40px' }}>Cargando...</div>
            ) : (
                <Form<FormValues>
                    form={form}
                    layout="vertical"
                    style={{ marginTop: '24px' }}
                >
                    {esUsuario ? (
                        <>
                            <Form.Item
                                label="Usuario"
                            >
                                <Input 
                                    value={cita?.usuario?.nombre || usuarioActual?.nombre || ''} 
                                    disabled 
                                    readOnly
                                />
                            </Form.Item>
                            <Form.Item
                                name="user_id"
                                hidden
                            >
                                <Input type="hidden" />
                            </Form.Item>
                        </>
                    ) : (
                        <Form.Item
                            label="Usuario"
                            name="user_id"
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
                    >
                        <TimePicker
                            style={{ width: '100%' }}
                            format="HH:mm"
                        />
                    </Form.Item>

                    <Form.Item
                        label="Estado"
                        name="estado"
                    >
                        <Select>
                            <Select.Option value="pendiente">Pendiente</Select.Option>
                            <Select.Option value="confirmada">Confirmada</Select.Option>
                            <Select.Option value="cancelada">Cancelada</Select.Option>
                        </Select>
                    </Form.Item>
                </Form>
            )}
        </Modal>
    );
}

