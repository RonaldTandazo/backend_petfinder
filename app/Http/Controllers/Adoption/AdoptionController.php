<?php

namespace App\Http\Controllers\Adoption;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adoption\AdoptionPetsRequest;
use App\Http\Requests\Adoption\FormAdoptionPetRequest;
use App\Http\Requests\Follow\FollowRequest;
use App\Http\Resources\Adoption\AdoptionPetListResource;
use App\Http\Resources\Adoption\AdoptionPetResource;
use App\Services\Adoption\AdoptionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AdoptionController extends Controller
{
    public function __construct(protected AdoptionService $adoptionService) {}

    public function getAdoptionPets(AdoptionPetsRequest $request): JsonResponse
    {
        try {
            $page  = $request->integer('page', 1);
            $limit = $request->integer('limit', 20);

            $result = $this->adoptionService->getAdoptionPets($page, $limit);

            $data = [
                'pets'    => AdoptionPetListResource::collection($result['items']),
                'hasMore' => $result['hasMore'],
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Listado de mascotas obtenido exitosamente',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener lista de mascotas: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el listado de mascotas',
                error   : $th->getMessage(),
            );
        }
    }

    public function getAdoptionPet(int $petId): JsonResponse{
        try {
            $pet = $this->adoptionService->getAdoptionPetById($petId);
            $isFollowing = $this->adoptionService->getAdoptionPetFollowState($petId, $this->getTutorId());

            $data = [
                'pet'          => AdoptionPetResource::make($pet),
                'is_following' => $isFollowing,
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Mascota obtenida exitosamente',
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Mascota no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener la mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener la mascota',
                error   : $th->getMessage()
            );
        }
    }

    public function store(FormAdoptionPetRequest $request): JsonResponse
    {
        try {
            $pet = $this->adoptionService->create($request->validated(), $this->getTutorId());

            return $this->sendResponse(
                data    : ['pet_id' => $pet->id],
                message : 'Mascota registrada exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo completar el registro del mascota',
                error   : $th->getMessage()
            );
        }
    }

    public function update(FormAdoptionPetRequest $request, int $petId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $pet = $this->adoptionService->update($petId, $tutorId, $request->validated());

            return $this->sendResponse(
                data    : ['pet_id' => $pet->id],
                message : 'Mascota actualizada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Mascota no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar mascota',
                error   : $th->getMessage()
            );
        }
    }

    public function delete(int $petId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $this->adoptionService->delete($petId, $tutorId);

            return $this->sendResponse(
                message: 'Mascota eliminada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Mascota no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar mascota',
                error   : $th->getMessage()
            );
        }
    }

    public function handleFollow(int $petId, FollowRequest $request): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();
            $followStatus = $request->boolean('is_following');

            $this->adoptionService->handleFollow($petId, $tutorId, $followStatus);

            return $this->sendResponse(
                message: 'Estado de seguimiento actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Mascota no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando estado de seguimiento: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar el estado de seguimiento',
                error   : $th->getMessage()
            );
        }
    }
}
