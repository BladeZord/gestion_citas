# Sistema de Gestión de Citas

Sistema completo de agendamiento de citas compuesto por una API REST backend y un portal web frontend.

## 📁 Estructura del Repositorio

Este repositorio contiene dos proyectos principales:

```
gestion_citas/
├── Sol_agendamiento_cita/      # API REST Backend (Laravel)
└── portal_agendamiento_cita/   # Frontend Web (React + TypeScript)
```

### Backend - API REST (`Sol_agendamiento_cita/`)

API REST desarrollada con **Laravel 11** que proporciona los endpoints para la gestión de citas, tipos de cita y usuarios.

**Tecnologías:**
- PHP 8.2+
- Laravel 11
- MySQL 8.0+
- Docker (opcional)

**Documentación completa:** Ver [Sol_agendamiento_cita/README.md](./Sol_agendamiento_cita/README.md)

### Frontend - Portal Web (`portal_agendamiento_cita/`)

Aplicación web desarrollada con **React 18** y **TypeScript** que consume la API REST para proporcionar una interfaz de usuario completa.

**Tecnologías:**
- React 18.3
- TypeScript
- Ant Design 6.0
- React Router DOM 6
- Axios

**Documentación completa:** Ver [portal_agendamiento_cita/README.md](./portal_agendamiento_cita/README.md)

## 🚀 Inicio Rápido

### Prerrequisitos

- **Docker y Docker Compose** (Recomendado - levanta todo el sistema)
- O alternativamente:
  - **Backend:** PHP 8.2+, Composer, MySQL 8.0+
  - **Frontend:** Node.js 16+, npm o yarn

### Instalación con Docker Compose (Recomendado)

Esta es la forma más sencilla de levantar todo el sistema completo:

```bash
# 1. Configurar variables de entorno (opcional)
cp docker-compose.env.example .env

# 2. Construir y levantar todos los servicios
docker-compose up -d --build

# 3. Generar clave de aplicación de Laravel
docker-compose exec backend php artisan key:generate

# 4. Ejecutar migraciones y seeders
docker-compose exec backend php artisan migrate --seed
```

**Servicios disponibles:**
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000
- **MySQL:** localhost:3306

**Comandos útiles:**
```bash
# Ver logs de todos los servicios
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f backend
docker-compose logs -f frontend

# Detener todos los servicios
docker-compose down

# Detener y eliminar volúmenes (incluye base de datos)
docker-compose down -v

# Reconstruir un servicio específico
docker-compose up -d --build frontend
```

### Instalación Manual (Sin Docker)

#### 1. Backend (API REST)

```bash
cd Sol_agendamiento_cita

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

La API estará disponible en `http://localhost:8000`

#### 2. Frontend (Portal Web)

```bash
cd portal_agendamiento_cita
npm install

# Crear archivo .env
echo "REACT_APP_API_URL=http://localhost:8000/api" > .env

npm start
```

El portal estará disponible en `http://localhost:3000`

## 📋 Funcionalidades

- ✅ Autenticación de usuarios (Admin y Usuario)
- ✅ Gestión de tipos de cita
- ✅ Agendamiento de citas
- ✅ Consulta y filtrado de citas
- ✅ Actualización de estado de citas
- ✅ Interfaz responsive

## 🔐 Usuarios por Defecto

- **Administrador:** admin@example.com / password123
- **Usuario:** usuario@example.com / password123

## 📚 Documentación de la API

La documentación completa de los endpoints está disponible en:
- [Sol_agendamiento_cita/README.md](./Sol_agendamiento_cita/README.md)
- [Sol_agendamiento_cita/API_DOCUMENTATION.md](./Sol_agendamiento_cita/API_DOCUMENTATION.md)

## 🛠️ Desarrollo

### Estructura de Base de Datos

- **usuarios**: id, nombre, correo, password, rol, timestamps
- **tipos_cita**: id, nombre, timestamps
- **citas**: id, user_id, tipo_cita_id, fecha_cita, hora_cita, estado, timestamps

### Variables de Entorno

**Backend (.env):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendamiento_citas
DB_USERNAME=root
DB_PASSWORD=
```

**Frontend (.env):**
```env
REACT_APP_API_URL=http://localhost:8000/api
```

## 🐳 Configuración Docker Compose

El archivo `docker-compose.yml` en la raíz del proyecto orquesta todos los servicios:

- **mysql**: Base de datos MySQL 8.0
- **backend**: API REST Laravel con PHP-FPM y Nginx
- **frontend**: Aplicación React servida con Nginx

### Variables de Entorno para Docker Compose

Copia `docker-compose.env.example` a `.env` y ajusta según necesites:

```bash
cp docker-compose.env.example .env
```

Variables principales:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Configuración de MySQL
- `BACKEND_PORT`: Puerto del backend (default: 8000)
- `FRONTEND_PORT`: Puerto del frontend (default: 3000)
- `REACT_APP_API_URL`: URL de la API para el frontend (default: http://localhost:8000/api)

### Solución de Problemas

**Error: Puerto ya en uso**
```bash
# Cambiar los puertos en el archivo .env
BACKEND_PORT=8001
FRONTEND_PORT=3001
```

**Error: Permisos en Laravel**
```bash
docker-compose exec backend chmod -R 775 storage bootstrap/cache
docker-compose exec backend chown -R www-data:www-data storage bootstrap/cache
```

**Reconstruir contenedores después de cambios**
```bash
docker-compose down
docker-compose up -d --build
```

**Ver logs de errores**
```bash
# Todos los servicios
docker-compose logs -f

# Servicio específico
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f mysql
```

**Reiniciar base de datos desde cero**
```bash
docker-compose down -v
docker-compose up -d
docker-compose exec backend php artisan migrate --seed
```

## 📝 Notas

- Asegúrate de que el backend esté corriendo antes de iniciar el frontend
- El frontend se conecta a la API mediante la variable de entorno `REACT_APP_API_URL`
- Para producción, actualiza las URLs en los archivos de configuración de entorno
- En Docker, los servicios se comunican usando los nombres de los servicios como hostnames (mysql, backend, frontend)

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es privado.

