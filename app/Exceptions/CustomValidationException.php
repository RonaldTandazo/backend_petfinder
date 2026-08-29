<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomValidationException extends Exception
{
    protected array $errors;

    public function __construct(string|array $errors, string $message = 'Los datos proporcionados no son válidos.')
    {
        $this->errors = $this->normalizeErrors($errors);

        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function normalizeErrors(string|array $errors): array
    {
        if (is_string($errors)) {
            return ['error' => [$errors]];
        }

        return collect($errors)
            ->map(fn ($messages) => is_array($messages) ? array_values($messages) : [$messages])
            ->toArray();
    }
}
