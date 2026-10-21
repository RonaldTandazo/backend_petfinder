<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterShelterRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\AuthService;
use App\Services\Legal\LegalDocumentAcceptanceService;
use App\Services\Legal\LegalDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected LegalDocumentService $legalDocumentService,
        protected LegalDocumentAcceptanceService $legalDocumentAcceptanceService
    ) {}

    public function registerUser(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->registerUser($request->validated());
            $legalDocuments = $this->legalDocumentService->getCurrentVersions();

            $legalAcceptances = [];

            foreach ($legalDocuments as $document => $detail) {
                $legalAcceptances[] = [
                    'tutor_id'                  => $user->tutor->id,
                    'legal_document_version_id' => $detail['version_id'],
                    'accepted_at'               => now(),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ];
            }

            $this->legalDocumentAcceptanceService->store($legalAcceptances);

            return $this->sendResponse(
                message : 'Usuario registrado exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando usuario: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo completar el registro del usuario',
                error   : $th->getMessage()
            );
        }
    }

    public function registerShelter(RegisterShelterRequest $request): JsonResponse
    {
        try {
            $shelter = $this->authService->registerShelter($request->validated());
            $legalDocuments = $this->legalDocumentService->getCurrentVersions();

            $legalAcceptances = [];

            foreach ($legalDocuments as $document => $detail) {
                $legalAcceptances[] = [
                    'tutor_id'                  => $shelter->tutor->id,
                    'legal_document_version_id' => $detail['version_id'],
                    'accepted_at'               => now(),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ];
            }

            $this->legalDocumentAcceptanceService->store($legalAcceptances);

            return $this->sendResponse(
                message : 'Refugio registrado exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando refugio: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo completar el registro del refugio',
                error   : $th->getMessage()
            );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return match ($result['status']) {
                'invalid_credentials' => $this->sendError(
                    message : 'Las credenciales ingresadas son incorrectas',
                    code    : Response::HTTP_UNAUTHORIZED
                ),
                'select_account' => $this->sendResponse(
                    data    : $result['payload'],
                    message : 'Seleccione con qué tipo de cuenta desea ingresar'
                ),
                'success' => $this->sendResponse(
                    data    : $result['payload'],
                    message : 'Inicio de sesión exitoso'
                ),
            };
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error en login: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'Error al intentar iniciar sesión',
                error   : $th->getMessage()
            );
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $account = $request->user();

            if (!$account) {
                return $this->sendError(
                    message : 'Token no válido o usuario no encontrado',
                    code    : Response::HTTP_UNAUTHORIZED
                );
            }

            return $this->sendResponse(
                data    : $this->authService->sessionInfo($account, $this->isUser()),
                message : 'Sesión verificada exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error validando token: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'Error al verificar la sesión',
                error   : $th->getMessage(),
                code    : Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return $this->sendResponse(
                message: 'Sesión cerrada correctamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error en logout: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'Error al cerrar sesión',
                error   : $th->getMessage()
            );
        }
    }
}
