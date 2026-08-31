<?php

namespace App\Http\Controllers\LostPet;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LostPet\FormLostPetRequest;
use App\Http\Requests\LostPet\LostPetsReportsRequest;
use App\Http\Resources\LostPet\LostPetListResource;
use App\Services\LostPet\LostPetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LostPetController extends Controller
{
    public function __construct(protected LostPetService $lostPetService) {}

    public function getLostPets(LostPetsReportsRequest $request): JsonResponse
    {
        try {
            $page  = $request->integer('page', 1);
            $limit = $request->integer('limit', 20);

            $result = $this->lostPetService->getLostPets($page, $limit);

            $data = [
                'lost_pets' => LostPetListResource::collection($result['items']),
                'hasMore'   => $result['hasMore'],
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Reportes de mascotas perdidas obtenido exitosamente',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener reportes de mascotas perdidas: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudieron obtener los reportes de mascotas perdidas',
                error   : $th->getMessage(),
            );
        }
    }

    public function store(FormLostPetRequest $request): JsonResponse
    {
        try {
            $lostPet = $this->lostPetService->create($request->validated(), $this->getTutorId());

            return $this->sendResponse(
                data    : ['lost_pet_id' => $lostPet->id],
                message : 'Reporte de Mascota Perdida registrada exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando reporte de mascota perdida: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo completar el reporte de mascota perdida',
                error   : $th->getMessage()
            );
        }
    }

    public function update(FormLostPetRequest $request, int $lostPetId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $lostPet = $this->lostPetService->update($lostPetId, $tutorId, $request->validated());

            return $this->sendResponse(
                data    : ['lost_pet_id' => $lostPet->id],
                message : 'Reporte de Mascota Perdida actualizada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Reporte de Mascota Perdida no encontrado',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando reporte de mascota perdida: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar el reporte de mascota perdida',
                error   : $th->getMessage()
            );
        }
    }

    public function delete(int $lostPetId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $this->lostPetService->delete($lostPetId, $tutorId);

            return $this->sendResponse(
                message: 'Reporte de Mascota Perdida eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Reporte de Mascota Perdida no encontrado',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando reporte de mascota perdida: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar reporte de mascota perdida',
                error   : $th->getMessage()
            );
        }
    }
}
