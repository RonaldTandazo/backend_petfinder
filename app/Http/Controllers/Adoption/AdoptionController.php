<?php

namespace App\Http\Controllers\Adoption;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adoption\AdoptionPetsRequest;
use App\Http\Resources\Adoption\AdoptionPetResource;
use App\Services\Adoption\AdoptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
                'pets'    => AdoptionPetResource::collection($result['items']),
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
}
