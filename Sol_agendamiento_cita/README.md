# API REST - Sistema de Agendamiento de Citas

API REST desarrollada con Laravel 11 para el sistema de agendamiento de citas.

## Requisitos

- PHP 8.2 o superior
- Composer
- MySQL 8.0 o superior
- Docker y Docker Compose (opcional)

## Instalación

### Opción 1: Con Docker (Recomendado)

1. Clonar el repositorio
2. Copiar el archivo `.env.example` a `.env`:
   ```bash
   cp .env.example .env
   ```
3. Construir y levantar los contenedores:
   ```bash
   docker-compose up -d --build
   ```
4. Generar la clave de aplicación:
   ```bash
   docker-compose exec backend php artisan key:generate
   ```
5. Ejecutar migraciones y seeders:
   ```bash
   docker-compose exec backend php artisan migrate --seed
   ```

La API estará disponible en `http://localhost:8000`

### Opción 2: Instalación Local

1. Instalar dependencias:
   ```bash
   composer install
   ```
2. Copiar `.env.example` a `.env` y configurar la base de datos
3. Generar clave de aplicación:
   ```bash
   php artisan key:generate
   ```
4. Ejecutar migraciones:
   ```bash
   php artisan migrate --seed
   ```
5. Iniciar servidor:
   ```bash
   php artisan serve
   ```

## Estructura de la Base de Datos

- **usuarios**: id, nombre, correo, password, rol, timestamps
- **tipos_cita**: id, nombre, timestamps
- **citas**: id, user_id, tipo_cita_id, fecha_cita, hora_cita, estado, timestamps

## Autenticación

### Login

**POST** `/api/login`

Body:
```json
{
  "correo": "admin@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "codigo": 200,
  "descripcion": "OK",
  "mensaje": "Login exitoso",
  "datos": {
    "token": "token_generado",
    "usuario": {
      "id": 1,
      "nombre": "Administrador",
      "correo": "admin@example.com",
      "rol": "admin"
    }
  }
}
```

Para usar las rutas protegidas, incluir el token en el header:
```
Authorization: Bearer {token}
```

## Endpoints

### Tipos de Cita

- **GET** `/api/tipos-cita` - Listar tipos de cita
- **POST** `/api/tipos-cita` - Crear tipo de cita
- **PUT** `/api/tipos-cita/{id}` - Actualizar tipo de cita
- **DELETE** `/api/tipos-cita/{id}` - Eliminar tipo de cita

### Citas

- **GET** `/api/citas` - Listar citas (con paginación y filtros)
  - Filtros: `fecha_cita`, `user_id`, `tipo_cita_id`, `estado`
- **POST** `/api/citas` - Crear cita
- **GET** `/api/citas/{id}` - Obtener detalle de cita
- **PUT** `/api/citas/{id}` - Actualizar cita
- **PUT** `/api/citas/{id}/estado` - Actualizar solo el estado
- **DELETE** `/api/citas/{id}` - Eliminar cita

## Estructura de Respuestas

Todas las respuestas siguen el formato:

```json
{
  "codigo": 200,
  "descripcion": "OK",
  "mensaje": "Mensaje descriptivo",
  "datos": { ... }
}
```

Códigos HTTP:
- 200: OK
- 201: Creado exitosamente
- 400: La entrada es incorrecta
- 401: No autorizado
- 404: Recurso no encontrado
- 422: Error de validación
- 500: Error interno del servidor

## Validaciones

### Reglas de Negocio

- Un usuario no puede tener dos citas en la misma fecha y hora
- Se valida la existencia de `user_id` y `tipo_cita_id` antes de crear/actualizar
- El estado solo puede ser: `pendiente`, `confirmada`, `cancelada`
- La fecha de la cita debe ser hoy o una fecha futura

## Usuarios por Defecto

- **Admin**: admin@example.com / password123
- **Usuario**: usuario@example.com / password123

## Script SQL

El archivo `db_dump.sql` contiene la estructura completa de la base de datos y datos iniciales.

