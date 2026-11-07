<?php

namespace App\Http\Controllers\Follow;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Follow\FollowRequest;
use App\Http\Requests\Follow\OnFollowRequest;
use App\Http\Resources\Follow\FollowPetListResource;
use App\Services\Adoption\AdoptionService;
use App\Services\Follow\FollowService;
use App\Services\LostPet\LostPetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FollowController extends Controller
{
    public function __construct(
        protected FollowService $followService,
        protected AdoptionService $adoptionService,
        protected LostPetService $lostPetService
    ) {}

    public function petPetFollowindex(): JsonResponse
    {
        try {
            $counters = $this->followService->getPetFollowsCount($this->getTutorId());

            $data = [
                'counters' => $counters,
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Index obtenido exitosamente',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener index de pet follows: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener los favoritos',
                error   : $th->getMessage(),
            );
        }
    }

    public function getPetFollows(FollowRequest $request): JsonResponse
    {
        try {
            $category = $request->safe()['category'];

            $page  = $request->integer('page', 1);
            $limit = $request->integer('limit', 20);

            $result = $this->followService->getPetFollows($page, $limit, $this->getTutorId(), $category);

            $data = [
                'follows' => FollowPetListResource::collection($result['items']),
                'hasMore' => $result['hasMore'],
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Guardados obtenido exitosamente',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener las mascotas favoritas: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener las mascotas favoritas',
                error   : $th->getMessage(),
            );
        }
    }

    public function setPetFollow(OnFollowRequest $request): JsonResponse
    {
        try {
            $petId = $request->safe()['pet_id'];
            $petType = $request->safe()['pet_type'];
            $state = $request->safe()->boolean('is_following');

            $this->followService->setPetFollow($petId, $this->getTutorId(), $state, $petType);

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
