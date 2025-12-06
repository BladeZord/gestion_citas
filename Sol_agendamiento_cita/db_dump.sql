-- Estructura de base de datos para sistema de agendamiento de citas
-- MySQL 8.0+

CREATE DATABASE IF NOT EXISTS `agendamiento_citas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `agendamiento_citas`;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_correo_unique` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: tipos_cita
CREATE TABLE IF NOT EXISTS `tipos_cita` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_cita_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_cita` time NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_fecha_hora` (`user_id`,`fecha_cita`,`hora_cita`),
  KEY `citas_tipo_cita_id_foreign` (`tipo_cita_id`),
  CONSTRAINT `citas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_tipo_cita_id_foreign` FOREIGN KEY (`tipo_cita_id`) REFERENCES `tipos_cita` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales (opcional)
-- Usuarios de ejemplo (password: password123)
INSERT INTO `usuarios` (`nombre`, `correo`, `password`, `rol`, `created_at`, `updated_at`) VALUES
('Administrador', 'admin@example.com', '$2y$12$qHvrdOuhlnAKEye5UV4zb.rt9O3FGj/UrgMstdDI4SIE3GM4njN2i', 'admin', NOW(), NOW()),
('Usuario Test', 'usuario@example.com', '$2y$12$qHvrdOuhlnAKEye5UV4zb.rt9O3FGj/UrgMstdDI4SIE3GM4njN2i', 'usuario', NOW(), NOW());

-- Tipos de cita por defecto
INSERT INTO `tipos_cita` (`nombre`, `created_at`, `updated_at`) VALUES
('Consulta General', NOW(), NOW()),
('Consulta Especializada', NOW(), NOW()),
('Revisión', NOW(), NOW()),
('Seguimiento', NOW(), NOW());

