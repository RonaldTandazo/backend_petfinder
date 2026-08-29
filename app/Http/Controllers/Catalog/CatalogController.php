<?php

namespace App\Http\Controllers\Catalog;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\AnimalGenderResource;
use App\Http\Resources\Catalog\HealthConditionResource;
use App\Http\Resources\Catalog\SizeResource;
use App\Http\Resources\Catalog\SpeciesResource;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogController extends Controller
{
    public function __construct(protected CatalogService $catalogService) {}

    public function getPublishPetCatalog(Request $request): JsonResponse
    {
        try {
            $catalog = $this->catalogService->getPublishPetCatalog();

            $data = [
                'species'           => SpeciesResource::collection($catalog['species']),
                'genders'           => AnimalGenderResource::collection($catalog['genders']),
                'sizes'             => SizeResource::collection($catalog['sizes']),
                'health_conditions' => HealthConditionResource::collection($catalog['health_conditions']),
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Listado de catálogo para publicar mascota obtenido',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener los catálogos: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener los catálogos',
                error   : $th->getMessage(),
            );
        }
    }
}
