<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * Obtiene la entidad autenticada actual (User o Shelter).
     */
    protected function authenticatedUser(): ?Authenticatable
    {
        return request()->user();
    }

    /**
     * Comprueba si la entidad autenticada es un Usuario particular.
     */
    protected function isUser(): bool
    {
        return $this->authenticatedUser() instanceof User;
    }

    /**
     * Comprueba si la entidad autenticada es un Refugio.
     */
    protected function isShelter(): bool
    {
        return $this->authenticatedUser() instanceof Shelter;
    }

    /**
     * Obtiene el ID principal según la entidad autenticada.
     */
    protected function getMainId(): ?int
    {
        return $this->authenticatedUser()?->id;
    }

    /**
     * Obtiene el ID del Tutor asociado según la entidad autenticada.
     */
    protected function getTutorId(): ?int
    {
        return $this->authenticatedUser()?->tutor?->id;
    }

    /**
     * Respuesta exitosa estandarizada.
     */
    protected function sendResponse(
        mixed $data = null,
        string $message = 'Operación realizada con éxito.',
        int $code = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'ok'      => true,
            'data'    => $data,
            'message' => $message,
            'code'    => $code,
        ], $code);
    }

    /**
     * Respuesta de error estandarizada.
     */
    protected function sendError(
        string $message = 'Ha ocurrido un error inesperado.',
        mixed $error = null,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return response()->json([
            'ok'      => false,
            'message' => $message,
            'code'    => $code,
            'error'   => $error,
        ], $code);
    }
}
