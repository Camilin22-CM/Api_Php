<?php
/**
 * Punto de entrada principal - Router
 */

// Configurar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar configuración
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $base = __DIR__ . '/../';
    $file = $base . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use Src\Controllers\AuthController;
use Src\Controllers\UserController;
use Src\Models\User;
use Src\Utils\Response;

// Crear tabla si no existe
$userModel = new User();
try {
    $userModel->createTable();
} catch (\Exception $e) {
    // La tabla ya existe o hay error de conexión
}

// Obtener método HTTP y ruta
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path); // Remover prefijo /api
$path = trim($path, '/');

// Router
try {
    switch (true) {
        // AUTENTICACIÓN
        case $path === 'auth/register' && $method === 'POST':
            $controller = new AuthController();
            $controller->register();
            break;

        case $path === 'auth/login' && $method === 'POST':
            $controller = new AuthController();
            $controller->login();
            break;

        case $path === 'auth/profile' && $method === 'GET':
            $controller = new AuthController();
            $controller->getProfile();
            break;

        case $path === 'auth/logout' && $method === 'POST':
            $controller = new AuthController();
            $controller->logout();
            break;

        // USUARIOS
        case $path === 'users' && $method === 'GET':
            $controller = new UserController();
            $controller->getAll();
            break;

        case preg_match('/^users\/(\d+)$/', $path, $matches) && $method === 'GET':
            $controller = new UserController();
            $controller->getById($matches[1]);
            break;

        case preg_match('/^users\/(\d+)$/', $path, $matches) && $method === 'PUT':
            $controller = new UserController();
            $controller->update($matches[1]);
            break;

        case preg_match('/^users\/(\d+)$/', $path, $matches) && $method === 'DELETE':
            $controller = new UserController();
            $controller->delete($matches[1]);
            break;

        // Ruta raíz
        case $path === '' && $method === 'GET':
            Response::success('API REST PHP - v1.0.0', [
                'endpoints' => [
                    'auth' => [
                        'POST /api/auth/register' => 'Registrar nuevo usuario',
                        'POST /api/auth/login' => 'Iniciar sesión',
                        'GET /api/auth/profile' => 'Obtener perfil (requiere token)',
                        'POST /api/auth/logout' => 'Cerrar sesión'
                    ],
                    'users' => [
                        'GET /api/users' => 'Obtener todos los usuarios',
                        'GET /api/users/{id}' => 'Obtener usuario por ID',
                        'PUT /api/users/{id}' => 'Actualizar usuario',
                        'DELETE /api/users/{id}' => 'Eliminar usuario'
                    ]
                ]
            ]);
            break;

        // Ruta no encontrada
        default:
            Response::notFound('Endpoint no encontrado');
            break;
    }
} catch (\Exception $e) {
    Response::error('Error interno: ' . $e->getMessage(), 500);
}
