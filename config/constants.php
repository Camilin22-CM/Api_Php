<?php
/**
 * Constantes de la Aplicación
 */

define('APP_NAME', 'API REST PHP');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Configuración JWT
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'tu-clave-secreta-super-segura-2024');
define('JWT_EXPIRATION', 3600); // 1 hora en segundos

// Configuración de respuestas
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_CONFLICT', 409);
define('HTTP_INTERNAL_ERROR', 500);

// Mensajes de error
define('MSG_SUCCESS', 'Operación exitosa');
define('MSG_CREATED', 'Recurso creado exitosamente');
define('MSG_ERROR', 'Error en la operación');
define('MSG_UNAUTHORIZED', 'No autorizado');
define('MSG_NOT_FOUND', 'Recurso no encontrado');
define('MSG_INVALID_DATA', 'Datos inválidos');
