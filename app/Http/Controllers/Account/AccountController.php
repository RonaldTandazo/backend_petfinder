<?php

namespace App\Http\Controllers\Account;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\UserResource;
use App\Services\Account\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    public function formCatalog(Request $request): JsonResponse
    {
        try {
            return $this->sendResponse(
                data    : $this->accountService->formCatalog($request->user()),
                message : 'Create de mi perfil obtenido exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error obteniendo vista create del formulario de mi perfil: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener la vista create del formulario de mi perfil',
                error   : $th->getMessage()
            );
        }
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $account = $this->accountService->updateProfile($request->user(), $request->validated());

            $resource = $this->isShelter()
                ? new ShelterResource($account)
                : new UserResource($account);

            return $this->sendResponse(
                data    : $resource,
                message : 'Perfil actualizado exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando perfil: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar el perfil',
                error   : $th->getMessage()
            );
        }
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->accountService->updatePassword($request->user(), $request->validated());

            return $this->sendResponse(
                message : 'Contraseña actualizada exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando contraseña: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar la contraseña',
                error   : $th->getMessage()
            );
        }
    }
}