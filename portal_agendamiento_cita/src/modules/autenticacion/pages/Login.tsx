import React, { useState } from 'react';
import { Form, Input, Button, Card, Typography, message, Space } from 'antd';
import { UserOutlined, LockOutlined } from '@ant-design/icons';
import { useNavigate } from 'react-router-dom';
import { authService } from '../services/auth.service';
import { LoginRequest } from '../interfaces/auth.interface';

const { Title, Text } = Typography;

export default function Login(): React.ReactElement {
    const [form] = Form.useForm<LoginRequest>();
    const [loading, setLoading] = useState<boolean>(false);
    const navigate = useNavigate();

    const onFinish = async (values: LoginRequest): Promise<void> => {
        setLoading(true);
        try {
            const response = await authService.login(values);
            
            if (response.codigo === 200) {
                // Guardar el token
                authService.saveToken(response.datos.token);
                authService.saveUser(response.datos.usuario);
                // Mostrar mensaje de éxito
                message.success(response.mensaje || 'Login exitoso');
                
                // Redirigir al dashboard o página principal
                navigate('/dashboard');
            } else {
                message.error(response.mensaje || 'Error al iniciar sesión');
            }
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al iniciar sesión. Por favor, intenta nuevamente.';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            minHeight: '100vh',
            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            padding: '20px'
        }}>
            <Card
                style={{
                    width: '100%',
                    maxWidth: '400px',
                    boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
                }}
            >
                <Space direction="vertical" size="large" style={{ width: '100%' }}>
                    <div style={{ textAlign: 'center' }}>
                        <Title level={2} style={{ marginBottom: '8px' }}>
                            Iniciar Sesión
                        </Title>
                        <Text type="secondary">
                            Ingresa tus credenciales para acceder
                        </Text>
                    </div>

                    <Form<LoginRequest>
                        form={form}
                        name="login"
                        onFinish={onFinish}
                        layout="vertical"
                        size="large"
                        autoComplete="off"
                    >
                        <Form.Item
                            name="correo"
                            label="Correo electrónico"
                            rules={[
                                { required: true, message: 'Por favor ingresa tu correo electrónico' },
                                { type: 'email', message: 'Por favor ingresa un correo válido' }
                            ]}
                        >
                            <Input
                                prefix={<UserOutlined />}
                                placeholder="usuario@example.com"
                                autoComplete="email"
                            />
                        </Form.Item>

                        <Form.Item
                            name="password"
                            label="Contraseña"
                            rules={[
                                { required: true, message: 'Por favor ingresa tu contraseña' },
                                { min: 6, message: 'La contraseña debe tener al menos 6 caracteres' }
                            ]}
                        >
                            <Input.Password
                                prefix={<LockOutlined />}
                                placeholder="Ingresa tu contraseña"
                                autoComplete="current-password"
                            />
                        </Form.Item>

                        <Form.Item>
                            <Button
                                type="primary"
                                htmlType="submit"
                                loading={loading}
                                block
                                style={{ height: '45px' }}
                            >
                                Iniciar Sesión
                            </Button>
                        </Form.Item>
                    </Form>
                </Space>
            </Card>
        </div>
    );
}

