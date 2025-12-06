import React, { useState } from 'react';
import { Form, Input, Modal, message, Select } from 'antd';
import { usuarioService } from '../../services/usuario.service';
import { UsuarioRequest } from '../../interfaces/usuario.interface';

interface CrearUsuarioModalProps {
    open: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

export default function CrearUsuarioModal({ open, onClose, onSuccess }: CrearUsuarioModalProps): React.ReactElement {
    const [form] = Form.useForm<UsuarioRequest>();
    const [loading, setLoading] = useState<boolean>(false);

    const handleSubmit = async (): Promise<void> => {
        try {
            const values = await form.validateFields();
            setLoading(true);
            
            const data: UsuarioRequest = {
                nombre: values.nombre,
                correo: values.correo,
                password: values.password,
                rol: values.rol,
            };

            await usuarioService.crear(data);
            message.success('Usuario creado exitosamente');
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
                : 'Error al crear el usuario';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = (): void => {
        form.resetFields();
        onClose();
    };

    return (
        <Modal
            title="Crear Nuevo Usuario"
            open={open}
            onCancel={handleCancel}
            onOk={handleSubmit}
            confirmLoading={loading}
            width={600}
            okText="Crear"
            cancelText="Cancelar"
        >
            <Form<UsuarioRequest>
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
                    label="Contraseña"
                    name="password"
                    rules={[
                        { required: true, message: 'Por favor ingresa la contraseña' },
                        { min: 6, message: 'La contraseña debe tener al menos 6 caracteres' }
                    ]}
                >
                    <Input.Password placeholder="Contraseña" />
                </Form.Item>

                <Form.Item
                    label="Rol"
                    name="rol"
                    rules={[{ required: true, message: 'Por favor selecciona un rol' }]}
                    initialValue="usuario"
                >
                    <Select>
                        <Select.Option value="usuario">Usuario</Select.Option>
                        <Select.Option value="admin">Administrador</Select.Option>
                    </Select>
                </Form.Item>
            </Form>
        </Modal>
    );
}

