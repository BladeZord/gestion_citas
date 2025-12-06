# Portal de Agendamiento de Citas

Aplicación web frontend desarrollada con React 18 y TypeScript para el sistema de agendamiento de citas.

## Tecnologías

- **React 18.3** con TypeScript
- **Ant Design 6.0** - Biblioteca de componentes UI
- **React Router DOM 6** - Enrutamiento
- **Axios** - Cliente HTTP para comunicación con la API
- **Day.js** - Manejo de fechas

## Requisitos

- Node.js 16 o superior
- npm o yarn

## Instalación

1. Instalar dependencias:
   ```bash
   npm install
   ```

2. Configurar variables de entorno:
   - Crear archivo `.env` en la raíz del proyecto
   - Configurar la URL de la API:
     ```
     REACT_APP_API_URL=http://localhost:8000/api
     ```

## Scripts Disponibles

### `npm start`

Ejecuta la aplicación en modo desarrollo.
Abre [http://localhost:3000](http://localhost:3000) en el navegador.

La página se recargará automáticamente cuando hagas cambios.

### `npm run build`

Construye la aplicación para producción en la carpeta `build`.
Optimiza y minifica el código para el mejor rendimiento.

### `npm test`

Ejecuta las pruebas en modo interactivo.

## Estructura del Proyecto

```
portal_agendamiento_cita/
├── public/          # Archivos estáticos
├── src/
│   ├── components/  # Componentes reutilizables
│   ├── modules/     # Módulos de la aplicación
│   │   ├── autenticacion/ # Modulo de autenticacion al portal
│   │   ├── mantenimiento/ # Modulo de gestion de tipos de citas, citas y usuarios
│   │   └── proceso/ # Proximamente
│   └── environment/ # Configuración de entornos
└── package.json
```

## Características

- Autenticación de usuarios
- Gestión de tipos de cita
- Agendamiento y gestión de citas
- Interfaz responsive con Ant Design

## Conexión con la API

Esta aplicación se conecta con la API REST ubicada en `Sol_agendamiento_cita/`. 
Asegúrate de que la API esté corriendo antes de iniciar el frontend.

Para más información sobre la API, consulta el README en `../Sol_agendamiento_cita/README.md`.
