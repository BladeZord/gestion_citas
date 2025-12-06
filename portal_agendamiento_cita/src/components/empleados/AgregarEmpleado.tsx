import axios from 'axios';
import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Form, Input, InputNumber, Button, Space, Typography, message } from 'antd';
import { Link } from 'react-router-dom';

const { Title } = Typography;

interface EmpleadoFormValues {
    nombre: string;
    departamento: string;
    sueldo: number;
}

export default function AgregarEmpleado(): React.ReactElement {
    const navegacion = useNavigate();
    const [form] = Form.useForm<EmpleadoFormValues>();

    const urlBase = "http://localhost:8080/rrhh-app/v1/es/empleados";

    const onSubmit = async (values: EmpleadoFormValues): Promise<void> => {
        try {
            await axios.post(urlBase, values);
            message.success('Empleado agregado correctamente');
            navegacion('/');
        } catch (error) {
            message.error('Error al agregar el empleado');
        }
    };

    return (
        <div>
            <div style={{ textAlign: 'center', marginBottom: '30px' }}>
                <Title level={3}>Agregar Empleado</Title>
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

