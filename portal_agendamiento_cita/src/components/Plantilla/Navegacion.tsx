import React from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { Layout, Menu, Button } from 'antd';
import { LogoutOutlined } from '@ant-design/icons';
import { MenuProps } from 'antd';
import { menuItems } from './menu-item';
import { authService } from '../../modules/autenticacion/services/auth.service';
import { message } from 'antd';

const { Header } = Layout;

export default function Navegacion(): React.ReactElement {
  const location = useLocation();
  const navigate = useNavigate();

  const handleLogout = (): void => {
    try {
      // Cerrar sesión usando el servicio
      authService.logout();
      
      // Mostrar mensaje de éxito
      message.success('Sesión cerrada exitosamente');
      
      // Redirigir al login
      navigate('/login');
    } catch (error) {
      message.error('Error al cerrar sesión');
    }
  };

  // Agregar opción de cerrar sesión al menú
  const menuItemsWithLogout: MenuProps['items'] = [
    ...(menuItems || []),
    {
      key: 'logout',
      label: (
        <Button
          type="text"
          danger
          icon={<LogoutOutlined />}
          onClick={handleLogout}
          style={{ color: '#fff', border: 'none' }}
        >
          Cerrar Sesión
        </Button>
      ),
      style: { marginLeft: 'auto' },
    },
  ];

  return (
    <Header style={{ display: 'flex', alignItems: 'center' }}>
      <div style={{ color: '#fff', fontSize: '18px', fontWeight: 'bold', marginRight: '24px' }}>
        Sistema de Agendamiento de Citas
      </div>
      <Menu
        theme="dark"
        mode="horizontal"
        selectedKeys={[location.pathname]}
        items={menuItemsWithLogout}
        style={{ flex: 1, minWidth: 0 }}
      />
    </Header>
  );
}

