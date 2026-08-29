<?php

namespace App\Helpers;

use App\Exceptions\CustomValidationException;

class ValidationErrorHelper
{
    /**
     * Lanza una excepción de validación personalizada (HTTP 422).
     */
    public static function throwValidationError(string|array $errors, string $message = 'Los datos proporcionados no son válidos.'): never
    {
        throw new CustomValidationException($errors, $message);
    }
}
