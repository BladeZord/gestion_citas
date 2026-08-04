import axios from 'axios';
import React, { useEffect, useCallback } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { Form, Input, InputNumber, Button, Space, Typography, message } from 'antd';

const { Title } = Typography;

interface EmpleadoFormValues {
    nombre: string;
    departamento: string;
    sueldo: number;
}

interface Empleado extends EmpleadoFormValues {
    idEmpleado: number;
}

export default function EditarEmpleado(): React.ReactElement {
    const navegacion = useNavigate();
    const urlBase = "http://localhost:8080/rrhh-app/v1/es/empleados";
    const { id } = useParams<{ id: string }>();
    const [form] = Form.useForm<EmpleadoFormValues>();

    const cargarEmpleado = useCallback(async (): Promise<void> => {
        if (!id) return;
        
        try {
            const resultado = await axios.get<Empleado>(`${urlBase}/${id}`);
            form.setFieldsValue({
                nombre: resultado.data.nombre,
                departamento: resultado.data.departamento,
                sueldo: resultado.data.sueldo
            });
        } catch (error) {
            message.error('Error al cargar el empleado');
        }
    }, [id, urlBase, form]);

    useEffect(() => {
        if (id) {
            cargarEmpleado();
        }
    }, [id, cargarEmpleado]);

    const onSubmit = async (values: EmpleadoFormValues): Promise<void> => {
        if (!id) return;
        
        try {
            await axios.put(`${urlBase}/${id}`, values);
            message.success('Empleado actualizado correctamente');
            navegacion('/');
        } catch (error) {
            message.error('Error al actualizar el empleado');
        }
    };

    return (
        <div>
            <div style={{ textAlign: 'center', marginBottom: '30px' }}>
                <Title level={3}>Editar Empleado</Title>
            </div>
            <Form<EmpleadoFormValues>
                form={form}
                layout="vertical"
                onFinish={onSubmit}
                style={{ maxWidth: '600px', margin: '0 auto' }}
            >
                <Form.Item
                    label="Nombre"
                    name="nombre"
                    rules={[{ required: true, message: 'Por favor ingresa el nombre' }]}
                >
                    <Input placeholder="Nombre del empleado" />
                </Form.Item>
                <Form.Item
                    label="Departamento"
                    name="departamento"
                    rules={[{ required: true, message: 'Por favor ingresa el departamento' }]}
                >
                    <Input placeholder="Departamento" />
                </Form.Item>
                <Form.Item
                    label="Sueldo"
                    name="sueldo"
                    rules={[{ required: true, message: 'Por favor ingresa el sueldo' }]}
                >
                    <InputNumber
                        style={{ width: '100%' }}
                        placeholder="Sueldo"
                        min={0}
                        step={0.01}
                        formatter={(value) => `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                        parser={(value: string | undefined): number => {
                            if (!value) return 0;
                            const parsed = value.replace(/\$\s?|(,*)/g, '');
                            return parseFloat(parsed) || 0;
                        }}
                    />
                </Form.Item>
                <Form.Item style={{ textAlign: 'center', marginTop: '24px' }}>
                    <Space>
                        <Button type="primary" htmlType="submit">
                            Guardar
                        </Button>
                        <Link to="/">
                            <Button danger>Regresar</Button>
                        </Link>
                    </Space>
                </Form.Item>
            </Form>
        </div>
    );
}

