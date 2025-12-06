import React, { useEffect, useState } from 'react';
import { Row, Col, Card, Statistic, Typography, Spin, message, Tag } from 'antd';
import { 
    CalendarOutlined, 
    CheckCircleOutlined, 
    ClockCircleOutlined, 
    CloseCircleOutlined,
    WarningOutlined
} from '@ant-design/icons';
import { citaService } from '../../services/cita.service';
import { DashboardStats } from '../../interfaces/cita.interface';

const { Title } = Typography;

export default function Dashboard(): React.ReactElement {
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        cargarDashboard();
    }, []);

    const cargarDashboard = async (): Promise<void> => {
        setLoading(true);
        try {
            const datos = await citaService.obtenerDashboard();
            setStats(datos);
        } catch (error) {
            const errorMessage = error instanceof Error 
                ? error.message 
                : 'Error al cargar el dashboard';
            message.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div style={{ textAlign: 'center', padding: '50px' }}>
                <Spin size="large" />
            </div>
        );
    }

    if (!stats) {
        return <div>No hay datos disponibles</div>;
    }

    return (
        <div>
            <Title level={2} style={{ marginBottom: '24px' }}>Dashboard</Title>
            
            {/* Estadísticas Principales */}
            <Row gutter={[16, 16]} style={{ marginBottom: '24px' }}>
                <Col xs={12} sm={8} lg={6}>
                    <Card size="small">
                        <Statistic
                            title="Total"
                            value={stats.total_citas}
                            prefix={<CalendarOutlined />}
                            valueStyle={{ fontSize: '20px' }}
                        />
                    </Card>
                </Col>
                <Col xs={12} sm={8} lg={6}>
                    <Card size="small">
                        <Statistic
                            title="Hoy"
                            value={stats.citas_hoy}
                            prefix={<CalendarOutlined />}
                            valueStyle={{ fontSize: '20px', color: '#52c41a' }}
                        />
                    </Card>
                </Col>
                <Col xs={12} sm={8} lg={6}>
                    <Card size="small">
                        <Statistic
                            title="Semana"
                            value={stats.citas_semana}
                            prefix={<CalendarOutlined />}
                            valueStyle={{ fontSize: '20px', color: '#722ed1' }}
                        />
                    </Card>
                </Col>
                <Col xs={12} sm={8} lg={6}>
                    <Card size="small">
                        <Statistic
                            title="Mes"
                            value={stats.citas_mes}
                            prefix={<CalendarOutlined />}
                            valueStyle={{ fontSize: '20px', color: '#fa8c16' }}
                        />
                    </Card>
                </Col>
            </Row>

            {/* Estados - Total */}
            <Row gutter={[16, 16]} style={{ marginBottom: '24px' }}>
                <Col xs={24}>
                    <Card size="small" title="Citas (Total)">
                        <Row gutter={[16, 8]}>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <ClockCircleOutlined style={{ fontSize: '24px', color: '#faad14' }} />
                                    <div style={{ fontSize: '18px', fontWeight: 'bold', marginTop: '8px' }}>
                                        {stats.citas_por_estado.pendiente}
                                    </div>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c' }}>Pendientes</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <CheckCircleOutlined style={{ fontSize: '24px', color: '#52c41a' }} />
                                    <div style={{ fontSize: '18px', fontWeight: 'bold', marginTop: '8px' }}>
                                        {stats.citas_por_estado.confirmada}
                                    </div>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c' }}>Confirmadas</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <CloseCircleOutlined style={{ fontSize: '24px', color: '#ff4d4f' }} />
                                    <div style={{ fontSize: '18px', fontWeight: 'bold', marginTop: '8px' }}>
                                        {stats.citas_por_estado.cancelada}
                                    </div>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c' }}>Canceladas</div>
                                </div>
                            </Col>
                        </Row>
                    </Card>
                </Col>
            </Row>

            {/* Estados - Hoy */}
            {/* <Row gutter={[16, 16]} style={{ marginBottom: '24px' }}>
                <Col xs={24}>
                    <Card size="small" title="Estados (Hoy)">
                        <Row gutter={[16, 8]}>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="orange" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_hoy_por_estado.pendiente}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Pendientes</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="green" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_hoy_por_estado.confirmada}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Confirmadas</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="red" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_hoy_por_estado.cancelada}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Canceladas</div>
                                </div>
                            </Col>
                        </Row>
                    </Card>
                </Col>
            </Row> */}

            {/* Estados - Semana */}
            <Row gutter={[16, 16]} style={{ marginBottom: '24px' }}>
                <Col xs={24}>
                    <Card size="small" title="Citas (Semana)">
                        <Row gutter={[16, 8]}>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="orange" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_semana_por_estado.pendiente}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Pendientes</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="green" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_semana_por_estado.confirmada}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Confirmadas</div>
                                </div>
                            </Col>
                            <Col xs={8} sm={8}>
                                <div style={{ textAlign: 'center' }}>
                                    <Tag color="red" style={{ fontSize: '14px', padding: '4px 12px' }}>
                                        {stats.citas_semana_por_estado.cancelada}
                                    </Tag>
                                    <div style={{ fontSize: '12px', color: '#8c8c8c', marginTop: '4px' }}>Canceladas</div>
                                </div>
                            </Col>
                        </Row>
                    </Card>
                </Col>
            </Row>

            {/* Alertas */}
            {/* <Row gutter={[16, 16]}>
                <Col xs={12} sm={12}>
                    <Card size="small">
                        <div style={{ textAlign: 'center' }}>
                            <CalendarOutlined style={{ fontSize: '20px', color: '#1890ff' }} />
                            <div style={{ fontSize: '16px', fontWeight: 'bold', marginTop: '8px' }}>
                                {stats.citas_proximas}
                            </div>
                            <div style={{ fontSize: '12px', color: '#8c8c8c' }}>Próximas (7 días)</div>
                        </div>
                    </Card>
                </Col>
                <Col xs={12} sm={12}>
                    <Card size="small">
                        <div style={{ textAlign: 'center' }}>
                            <WarningOutlined style={{ fontSize: '20px', color: '#ff4d4f' }} />
                            <div style={{ fontSize: '16px', fontWeight: 'bold', marginTop: '8px' }}>
                                {stats.citas_vencidas}
                            </div>
                            <div style={{ fontSize: '12px', color: '#8c8c8c' }}>Vencidas</div>
                        </div>
                    </Card>
                </Col>
            </Row> */}
        </div>
    );
}

