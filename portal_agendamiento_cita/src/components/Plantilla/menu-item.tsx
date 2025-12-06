import React from 'react';
import { Link } from 'react-router-dom';
import { MenuProps } from 'antd';
/* Opciones del menu de navegacion
- Inicio: Dashboard
- Citas: Listado de citas
- Tipos de cita: Listado de tipos de cita
- Usuarios: Listado de usuarios
*/
export const menuItems: MenuProps['items'] = [
    {
        key: '/dashboard',
        label: <Link to="/dashboard">Inicio</Link>,
    },
    {
        key: '/citas', // Listado de citas
        label: <Link to="/citas">Citas</Link>,
    },
    {
        key: '/tipos-cita', // Listado de tipos de cita
        label: <Link to="/tipos-cita">Tipos de cita</Link>,
    },
    {
        key: '/usuarios', // Listado de usuarios
        label: <Link to="/usuarios">Usuarios</Link>,
    },
];