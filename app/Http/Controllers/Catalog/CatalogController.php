<?php

namespace App\Http\Controllers\Catalog;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\AnimalGenderResource;
use App\Http\Resources\Catalog\CountryResource;
use App\Http\Resources\Catalog\GenderResource;
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

    public function getPetCatalogs(Request $request): JsonResponse
    {
        try {
            $catalogs = $this->catalogService->getPetCatalogs();

            $data = [
                'species'           => SpeciesResource::collection($catalogs['species']),
                'genders'           => AnimalGenderResource::collection($catalogs['genders']),
                'sizes'             => SizeResource::collection($catalogs['sizes']),
                'health_conditions' => HealthConditionResource::collection($catalogs['health_conditions']),
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Listado de catálogos para mascota obtenido',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener los catálogos de mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener los catálogos de mascota',
                error   : $th->getMessage(),
            );
        }
    }

    public function getAccountCatalogs(Request $request): JsonResponse
    {
        try {
            $catalogs = $this->catalogService->getAccountCatalogs();

            $data = [
                'countries' => CountryResource::collection($catalogs['countries']),
                'genders'   => GenderResource::collection($catalogs['genders']),
            ];

            return $this->sendResponse(
                data    : $data,
                message : 'Listado de catálogos para perfil obtenido',
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener los catálogos de perfil: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener los catálogos de perfil',
                error   : $th->getMessage(),
            );
        }
    }
}
