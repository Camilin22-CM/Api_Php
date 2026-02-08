<?php
/**
 * Controlador de Usuarios
 */

namespace Src\Controllers;

use Src\Models\User;
use Src\Utils\Response;
use Src\Utils\Validator;
use Src\Middleware\AuthMiddleware;

class UserController
{
    private $userModel;
    private $validator;

    public function __construct()
    {
        $this->userModel = new User();
        $this->validator = new Validator();
    }

    /**
     * Obtener todos los usuarios (solo administrador)
     */
    public function getAll()
    {
        try {
            // Verificar autenticación
            $payload = AuthMiddleware::verify();

            $users = $this->userModel->getAll();

            Response::success('Usuarios obtenidos', ['users' => $users]);
        } catch (\Exception $e) {
            Response::error('Error al obtener usuarios: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id)
    {
        try {
            // Verificar autenticación
            $payload = AuthMiddleware::verify();

            // Verificar que solo el usuario pueda ver sus datos
            AuthMiddleware::authorizeUser($payload['userId'], $id);

            $user = $this->userModel->findById($id);

            if (!$user) {
                return Response::notFound('Usuario no encontrado');
            }

            Response::success('Usuario obtenido', $user);
        } catch (\Exception $e) {
            Response::error('Error al obtener usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar usuario
     */
    public function update($id)
    {
        try {
            // Verificar autenticación
            $payload = AuthMiddleware::verify();

            // Verificar que solo el usuario pueda actualizar sus datos
            AuthMiddleware::authorizeUser($payload['userId'], $id);

            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (!$this->validateUpdate($data)) {
                return Response::validationError($this->validator->getErrors());
            }

            // Verificar que el usuario existe
            $user = $this->userModel->findById($id);
            if (!$user) {
                return Response::notFound('Usuario no encontrado');
            }

            // Actualizar usuario
            if ($this->userModel->update($id, $data)) {
                $updatedUser = $this->userModel->findById($id);
                Response::success('Usuario actualizado exitosamente', $updatedUser);
            } else {
                Response::error('No hay cambios para actualizar', 400);
            }
        } catch (\Exception $e) {
            Response::error('Error al actualizar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function delete($id)
    {
        try {
            // Verificar autenticación
            $payload = AuthMiddleware::verify();

            // Verificar que solo el usuario pueda eliminar su cuenta
            AuthMiddleware::authorizeUser($payload['userId'], $id);

            // Verificar que el usuario existe
            $user = $this->userModel->findById($id);
            if (!$user) {
                return Response::notFound('Usuario no encontrado');
            }

            // Eliminar usuario
            if ($this->userModel->delete($id)) {
                Response::success('Usuario eliminado exitosamente');
            } else {
                Response::error('No se pudo eliminar el usuario', 500);
            }
        } catch (\Exception $e) {
            Response::error('Error al eliminar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validar datos de actualización
     */
    private function validateUpdate($data)
    {
        $this->validator->clearErrors();

        if (isset($data['name'])) {
            if (!$this->validator->minLength($data['name'], 3, 'Nombre')) {
                return false;
            }
        }

        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!$this->validator->phone($data['phone'])) {
                return false;
            }
        }

        return !$this->validator->hasErrors();
    }
}
