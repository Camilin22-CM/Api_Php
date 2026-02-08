<?php
/**
 * Controlador de Autenticación
 */

namespace Src\Controllers;

use Src\Models\User;
use Src\Utils\Response;
use Src\Utils\Validator;
use Src\Utils\JWT;
use Src\Exceptions\CustomException;

class AuthController
{
    private $userModel;
    private $validator;

    public function __construct()
    {
        $this->userModel = new User();
        $this->validator = new Validator();
    }

    /**
     * Registro de nuevo usuario
     */
    public function register()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (!$this->validateRegistration($data)) {
                return Response::validationError($this->validator->getErrors());
            }

            // Verificar si el email ya existe
            if ($this->userModel->emailExists($data['email'])) {
                return Response::error('El email ya está registrado', 409);
            }

            // Crear usuario
            $user = $this->userModel->create(
                $data['name'],
                $data['email'],
                $data['password'],
                $data['phone'] ?? null
            );

            // Generar token
            $token = JWT::generate(['userId' => $user['id'], 'email' => $user['email']]);

            Response::success(
                'Usuario registrado exitosamente',
                [
                    'user' => $user,
                    'token' => $token
                ],
                201
            );
        } catch (\Exception $e) {
            Response::error('Error al registrar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login de usuario
     */
    public function login()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (!$this->validateLogin($data)) {
                return Response::validationError($this->validator->getErrors());
            }

            // Buscar usuario por email
            $user = $this->userModel->findByEmail($data['email']);

            if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
                return Response::unauthorized('Email o contraseña incorrectos');
            }

            // Generar token
            $token = JWT::generate(['userId' => $user['id'], 'email' => $user['email']]);

            Response::success(
                'Login exitoso',
                [
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'phone' => $user['phone']
                    ],
                    'token' => $token
                ]
            );
        } catch (\Exception $e) {
            Response::error('Error al iniciar sesión: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    public function getProfile()
    {
        try {
            // Verificar autenticación
            $payload = \Src\Middleware\AuthMiddleware::verify();

            // Obtener usuario
            $user = $this->userModel->findById($payload['userId']);

            if (!$user) {
                return Response::notFound('Usuario no encontrado');
            }

            Response::success('Perfil obtenido', $user);
        } catch (\Exception $e) {
            Response::error('Error al obtener perfil: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cerrar sesión (logout)
     */
    public function logout()
    {
        try {
            Response::success('Sesión cerrada exitosamente');
        } catch (\Exception $e) {
            Response::error('Error al cerrar sesión: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validar datos de registro
     */
    private function validateRegistration($data)
    {
        $this->validator->clearErrors();

        if (!isset($data['name']) || !$this->validator->required($data['name'], 'Nombre')) {
            return false;
        }

        if (!isset($data['email']) || !$this->validator->required($data['email'], 'Email')) {
            return false;
        }

        if (!$this->validator->email($data['email'])) {
            return false;
        }

        if (!isset($data['password']) || !$this->validator->required($data['password'], 'Contraseña')) {
            return false;
        }

        if (!$this->validator->minLength($data['password'], 6, 'Contraseña')) {
            return false;
        }

        if (!isset($data['password_confirmation']) || 
            !$this->validator->match($data['password'], $data['password_confirmation'], 'Contraseñas')) {
            return false;
        }

        if (!$this->validator->minLength($data['name'], 3, 'Nombre')) {
            return false;
        }

        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!$this->validator->phone($data['phone'])) {
                return false;
            }
        }

        return !$this->validator->hasErrors();
    }

    /**
     * Validar datos de login
     */
    private function validateLogin($data)
    {
        $this->validator->clearErrors();

        if (!isset($data['email']) || !$this->validator->required($data['email'], 'Email')) {
            return false;
        }

        if (!$this->validator->email($data['email'])) {
            return false;
        }

        if (!isset($data['password']) || !$this->validator->required($data['password'], 'Contraseña')) {
            return false;
        }

        return !$this->validator->hasErrors();
    }
}
