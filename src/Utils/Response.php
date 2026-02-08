<?php
/**
 * Clase para manejar respuestas JSON
 */

namespace Src\Utils;

class Response
{
    /**
     * Enviar respuesta JSON
     */
    public static function send($statusCode, $message, $data = null, $errors = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);

        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Respuesta exitosa
     */
    public static function success($message, $data = null, $statusCode = 200)
    {
        self::send($statusCode, $message, $data);
    }

    /**
     * Respuesta de error
     */
    public static function error($message, $statusCode = 400, $errors = null)
    {
        self::send($statusCode, $message, null, $errors);
    }

    /**
     * Respuesta no encontrada
     */
    public static function notFound($message = 'Recurso no encontrado')
    {
        self::send(404, $message);
    }

    /**
     * Respuesta no autorizada
     */
    public static function unauthorized($message = 'No autorizado')
    {
        self::send(401, $message);
    }

    /**
     * Respuesta de validación fallida
     */
    public static function validationError($errors)
    {
        self::send(400, 'Error de validación', null, $errors);
    }
}
