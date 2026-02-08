-- Script SQL para crear la base de datos y tabla de usuarios

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS api_db;
USE api_db;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para búsquedas rápidas
    KEY idx_email (email),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ejemplo de inserción de datos para prueba (opcional)
-- INSERT INTO users (name, email, password, phone) 
-- VALUES ('Usuario Prueba', 'usuario@example.com', '$2y$10$...', '+57 300 1234567');
