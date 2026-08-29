<?php

namespace App\Http\Controllers\Pet;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pet\FormPetRequest;
use App\Http\Resources\PetListResource;
use App\Http\Resources\PetDetailResource;
use App\Services\Pet\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class PetController extends Controller
{
    public function __construct(protected PetService $petService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $page  = max(1, $request->integer('page', 1));
            $limit = min(50, max(1, $request->integer('limit', 20)));

            $result = $this->petService->list($tutorId, $page, $limit);

            $data = [
                'hasMore' => $result['hasMore'],
                'pets'    => PetListResource::collection($result['items']),
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

    public function show(int $petId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $pet = $this->petService->find($petId, $tutorId);

            return $this->sendResponse(
                data    : $pet,
                message : 'Información de la mascota obtenida exitosamente'
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
            Log::error('Error al obtener detalle de la mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el detalle de la mascota',
                error   : $th->getMessage()
            );
        }
    }

    public function store(FormPetRequest $request): JsonResponse
    {
        try {
            $pet = $this->petService->create($request->validated(), $this->getTutorId(), $this->getMainId());

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

    public function update(FormPetRequest $request, int $petId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $pet = $this->petService->update($petId, $tutorId, $this->getMainId(), $request->validated());

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

            $this->petService->delete($petId, $tutorId);

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
}
