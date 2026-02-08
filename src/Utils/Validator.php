<?php
/**
 * Clase para validación de datos
 */

namespace Src\Utils;

class Validator
{
    private $errors = [];

    /**
     * Validar email
     */
    public function email($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email inválido";
            return false;
        }
        return true;
    }

    /**
     * Validar que no esté vacío
     */
    public function required($value, $fieldName = 'Campo')
    {
        if (empty(trim($value))) {
            $this->errors[] = "$fieldName es requerido";
            return false;
        }
        return true;
    }

    /**
     * Validar longitud mínima
     */
    public function minLength($value, $min, $fieldName = 'Campo')
    {
        if (strlen($value) < $min) {
            $this->errors[] = "$fieldName debe tener al menos $min caracteres";
            return false;
        }
        return true;
    }

    /**
     * Validar longitud máxima
     */
    public function maxLength($value, $max, $fieldName = 'Campo')
    {
        if (strlen($value) > $max) {
            $this->errors[] = "$fieldName no debe exceder $max caracteres";
            return false;
        }
        return true;
    }

    /**
     * Validar que coincidan dos valores
     */
    public function match($value1, $value2, $fieldName = 'Campos')
    {
        if ($value1 !== $value2) {
            $this->errors[] = "$fieldName no coinciden";
            return false;
        }
        return true;
    }

    /**
     * Validar formato de teléfono
     */
    public function phone($value)
    {
        if (!preg_match('/^[0-9\-\+\(\)\s]{7,20}$/', $value)) {
            $this->errors[] = "Teléfono inválido";
            return false;
        }
        return true;
    }

    /**
     * Validar caracteres alfanuméricos
     */
    public function alphanumeric($value, $fieldName = 'Campo')
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            $this->errors[] = "$fieldName solo puede contener letras, números, guiones y guiones bajos";
            return false;
        }
        return true;
    }

    /**
     * Obtener errores
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Verificar si hay errores
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Limpiar errores
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
}
