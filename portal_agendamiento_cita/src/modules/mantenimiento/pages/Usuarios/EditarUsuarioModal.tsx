import React, { useEffect, useState, useCallback } from 'react';
import { Form, Input, Modal, message, Select } from 'antd';
import { usuarioService } from '../../services/usuario.service';
import { UsuarioUpdateRequest, Usuario } from '../../interfaces/usuario.interface';

interface FormValues {
    nombre?: string;
    correo?: string;
    password?: string;
    rol?: 'admin' | 'usuario';
}

interface EditarUsuarioModalProps {
    open: boolean;
    usuarioId: number | null;
    onClose: () => void;
    onSuccess: () => void;
}

export default function EditarUsuarioModal({ open, usuarioId, onClose, onSuccess }: EditarUsuarioModalProps): React.ReactElement {
    const [form] = Form.useForm<FormValues>();
    const [loading, setLoading] = useState<boolean>(false);
    const [cargando, setCargando] = useState<boolean>(false);
    const [usuario, setUsuario] = useState<Usuario | null>(null);

    useEffect(() => {
        if (open && usuarioId) {
            cargarUsuario();
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open, usuarioId]);

    const cargarUsuario = useCallback(async (): Promise<void> => {
        if (!usuarioId) return;
        
        setCargando(true);
        try {
            const usuarioData = await usuarioService.obtenerPorId(usuarioId);
            setUsuario(usuarioData);
            
            form.setFieldsValue({
                nombre: usuarioData.nombre,
                correo: usuarioData.correo,
                rol: usuarioData.rol as 'admin' | 'usuario',
                // No establecer password por defecto
            });
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar el usuario';
            message.error(errorMessage);
            onClose();
        } finally {
            setCargando(false);
        }
    }, [usuarioId, form, onClose]);

    const handleSubmit = async (): Promise<void> => {
        if (!usuarioId) return;

        try {
            const values = await form.validateFields();
            setLoading(true);
            
            // Solo incluir password si se proporcionó uno nuevo
            const data: UsuarioUpdateRequest = {
                nombre: values.nombre,
                correo: values.correo,
                rol: values.rol,
            };

            // Solo agregar password si se proporcionó
            if (values.password && values.password.trim() !== '') {
                data.password = values.password;
            }

            await usuarioService.actualizar(usuarioId, data);
            message.success('Usuario actualizado exitosamente');
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
                : 'Error al actualizar el usuario';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = (): void => {
        form.resetFields();
        setUsuario(null);
        onClose();
    };

    return (
        <Modal
            title="Editar Usuario"
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
                    <Form.Item
                        label="Nombre"
                        name="nombre"
                        rules={[
                            { required: true, message: 'Por favor ingresa el nombre' },
                            { min: 3, message: 'El nombre debe tener al menos 3 caracteres' }
                        ]}
                    >
                        <Input placeholder="Nombre completo" />
                    </Form.Item>

                    <Form.Item
                        label="Correo Electrónico"
                        name="correo"
                        rules={[
                            { required: true, message: 'Por favor ingresa el correo' },
                            { type: 'email', message: 'Por favor ingresa un correo válido' }
                        ]}
                    >
                        <Input type="email" placeholder="correo@ejemplo.com" />
                    </Form.Item>

                    <Form.Item
                        label="Nueva Contraseña (dejar vacío para mantener la actual)"
                        name="password"
                        rules={[
                            { min: 6, message: 'La contraseña debe tener al menos 6 caracteres' }
                        ]}
                    >
                        <Input.Password placeholder="Dejar vacío para mantener la contraseña actual" />
                    </Form.Item>

                    <Form.Item
                        label="Rol"
                        name="rol"
                        rules={[{ required: true, message: 'Por favor selecciona un rol' }]}
                    >
                        <Select>
                            <Select.Option value="usuario">Usuario</Select.Option>
                            <Select.Option value="admin">Administrador</Select.Option>
                        </Select>
                    </Form.Item>
                </Form>
            )}
        </Modal>
    );
}

