<?php

namespace App\Http\Controllers\LostPet;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LostPet\FormLostPetEventRequest;
use App\Services\LostPet\LostPetEventService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LostPetEventController extends Controller
{
    public function __construct(protected LostPetEventService $lostPetEventService) {}

    public function store(FormLostPetEventRequest $request): JsonResponse
    {
        try {
            $lostPetEvent = $this->lostPetEventService->create($request->validated(), $this->getTutorId());

            return $this->sendResponse(
                data    : ['lost_pet_event_id' => $lostPetEvent->id],
                message : 'Evento de Mascota Perdida registrado exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando evento de mascota perdida: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo completar el evento de mascota perdida',
                error   : $th->getMessage()
            );
        }
    }

    public function delete(int $lostPetEventId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $this->lostPetEventService->delete($lostPetEventId, $tutorId);

            return $this->sendResponse(
                message: 'Evento de Mascota Perdida eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Evento de Mascota Perdida no encontrado',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando evento de mascota perdida: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar evento de mascota perdida',
                error   : $th->getMessage()
            );
        }
    }
}
