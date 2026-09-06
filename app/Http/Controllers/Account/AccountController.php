<?php

namespace App\Http\Controllers\Account;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ProfileRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Resources\Adoption\AdoptionPetListResource;
use App\Http\Resources\LostPet\LostPetListResource;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\UserResource;
use App\Services\Account\AccountService;
use App\Services\Adoption\AdoptionService;
use App\Services\LostPet\LostPetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService,
        protected AdoptionService $adoptionService,
        protected LostPetService $lostPetService
    ) {}

    public function getProfile(ProfileRequest $request): JsonResponse
    {
        try {
            $validated = $request->safe();

            $tutorId        = $validated['tutor_id'] ?? $this->getTutorId();
            $limit          = $validated['limit'] ?? 20;
            $page_adoptions = $validated['page_adoptions'] ?? 1;
            $page_lost_pets = $validated['page_lost_pets'] ?? 1;            

            $filters = [
                'tutor_id' => $tutorId
            ];

            $metrics = $this->accountService->getProfileMetrics($tutorId);
            $adoptions = $this->adoptionService->getAdoptionPets($filters, $page_adoptions, $limit);
            $lostPets = $this->lostPetService->getLostPets($filters, $page_lost_pets, $limit);

            $data = [
                'metrics'          => $metrics,
                'adoptions'        => AdoptionPetListResource::collection($adoptions['items']),
                'hasMoreAdoptions' => $adoptions['hasMore'],
                'lost_pets'        => LostPetListResource::collection($lostPets['items']),
                'hasMoreLostPets'  => $lostPets['hasMore'],
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Información del perfil obtenida exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error obteniendo la información del perfil: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener la información del perfil',
                error   : $th->getMessage()
            );
        }
    }

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