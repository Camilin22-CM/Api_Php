<?php
/**
 * Excepción personalizada
 */

namespace Src\Exceptions;

class CustomException extends \Exception
{
    protected $statusCode;
    protected $errors;

    public function __construct($message = "", $statusCode = 400, $errors = null, $code = 0)
    {
        parent::__construct($message, $code);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
