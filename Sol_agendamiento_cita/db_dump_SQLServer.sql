-- Estructura de base de datos para sistema de agendamiento de citas
-- SQL Server

IF DB_ID('agendamiento_citas') IS NULL
BEGIN
    CREATE DATABASE agendamiento_citas;
END;
GO

USE agendamiento_citas;
GO

-- Tabla: usuarios
IF OBJECT_ID('dbo.usuarios', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.usuarios (
        id BIGINT IDENTITY(1,1) NOT NULL,
        nombre VARCHAR(255) NOT NULL,
        correo VARCHAR(255) NOT NULL,
        [password] VARCHAR(255) NOT NULL,
        rol VARCHAR(20) NOT NULL CONSTRAINT DF_usuarios_rol DEFAULT 'usuario',
        remember_token VARCHAR(100) NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        CONSTRAINT PK_usuarios PRIMARY KEY (id),
        CONSTRAINT UQ_usuarios_correo UNIQUE (correo),
        CONSTRAINT CK_usuarios_rol CHECK (rol IN ('admin', 'usuario'))
    );
END;
GO

-- Tabla: tipos_cita
IF OBJECT_ID('dbo.tipos_cita', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.tipos_cita (
        id BIGINT IDENTITY(1,1) NOT NULL,
        nombre VARCHAR(255) NOT NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        CONSTRAINT PK_tipos_cita PRIMARY KEY (id)
    );
END;
GO

-- Tabla: citas
IF OBJECT_ID('dbo.citas', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.citas (
        id BIGINT IDENTITY(1,1) NOT NULL,
        user_id BIGINT NOT NULL,
        tipo_cita_id BIGINT NOT NULL,
        fecha_cita DATE NOT NULL,
        hora_cita TIME NOT NULL,
        estado VARCHAR(20) NOT NULL CONSTRAINT DF_citas_estado DEFAULT 'pendiente',
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        CONSTRAINT PK_citas PRIMARY KEY (id),
        CONSTRAINT UQ_citas_user_fecha_hora UNIQUE (user_id, fecha_cita, hora_cita),
        CONSTRAINT CK_citas_estado CHECK (estado IN ('pendiente', 'confirmada', 'cancelada')),
        CONSTRAINT FK_citas_user_id FOREIGN KEY (user_id)
            REFERENCES dbo.usuarios(id)
            ON DELETE CASCADE,
        CONSTRAINT FK_citas_tipo_cita_id FOREIGN KEY (tipo_cita_id)
            REFERENCES dbo.tipos_cita(id)
            ON DELETE CASCADE
    );

    CREATE INDEX IX_citas_tipo_cita_id ON dbo.citas(tipo_cita_id);
END;
GO

-- Datos iniciales (opcional)
-- Usuarios de ejemplo (password: password123)
INSERT INTO dbo.usuarios (nombre, correo, [password], rol, created_at, updated_at)
VALUES
('Administrador', 'admin@example.com', '$2y$12$qHvrdOuhlnAKEye5UV4zb.rt9O3FGj/UrgMstdDI4SIE3GM4njN2i', 'admin', GETDATE(), GETDATE()),
('Usuario Test', 'usuario@example.com', '$2y$12$qHvrdOuhlnAKEye5UV4zb.rt9O3FGj/UrgMstdDI4SIE3GM4njN2i', 'usuario', GETDATE(), GETDATE());
GO

-- Tipos de cita por defecto
INSERT INTO dbo.tipos_cita (nombre, created_at, updated_at)
VALUES
('Consulta General', GETDATE(), NULL),
('Consulta Especializada', GETDATE(), NULL),
('Revisión', GETDATE(), NULL),
('Seguimiento', GETDATE(), NULL);
GO