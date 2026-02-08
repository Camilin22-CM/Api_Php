<?php
/**
 * Middleware de Autenticación
 */

namespace Src\Middleware;

use Src\Utils\JWT;
use Src\Utils\Response;

class AuthMiddleware
{
    /**
     * Verificar autenticación
     */
    public static function verify()
    {
        $token = JWT::getTokenFromHeader();

        if (!$token) {
            Response::unauthorized('Token no proporcionado');
        }

        $payload = JWT::verify($token);

        if (!$payload) {
            Response::unauthorized('Token inválido o expirado');
        }

        return $payload;
    }

    /**
     * Verificar que solo el usuario pueda acceder a sus datos
     */
    public static function authorizeUser($userId, $resourceOwnerId)
    {
        if ($userId != $resourceOwnerId) {
            Response::error('No tienes permiso para acceder a este recurso', 403);
        }
    }
}
