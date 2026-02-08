<?php
/**
 * Clase para manejar JSON Web Tokens (JWT)
 */

namespace Src\Utils;

class JWT
{
    /**
     * Generar token JWT
     */
    public static function generate($payload)
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;

        $headerEncoded = self::base64urlEncode(json_encode($header));
        $payloadEncoded = self::base64urlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            JWT_SECRET,
            true
        );

        $signatureEncoded = self::base64urlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Verificar y decodificar token JWT
     */
    public static function verify($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            JWT_SECRET,
            true
        );

        $signatureExpected = self::base64urlEncode($signature);

        if ($signatureEncoded !== $signatureExpected) {
            return false;
        }

        $payload = json_decode(self::base64urlDecode($payloadEncoded), true);

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Codificar en base64url
     */
    private static function base64urlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodificar base64url
     */
    private static function base64urlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 4 - strlen($data) % 4));
    }

    /**
     * Extraer token del header Authorization
     */
    public static function getTokenFromHeader()
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            
            if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
