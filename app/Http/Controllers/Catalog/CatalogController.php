<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\AnimalGenderResource;
use App\Http\Resources\Catalog\HealthConditionResource;
use App\Http\Resources\Catalog\SizeResource;
use App\Http\Resources\Catalog\SpeciesResource;
use App\Models\Catalog\AnimalGender;
use App\Models\Catalog\Size;
use App\Models\Catalog\Species;
use App\Models\Catalog\HealthCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogController extends Controller
{
    public function getPublishPetCatalog(Request $request): JsonResponse
    {
        try {
            $species = SpeciesResource::collection(Species::select('id', 'name', 'tag')->get());

            $genders = AnimalGenderResource::collection(AnimalGender::select('id', 'name', 'tag')->get());
            
            $sizes = SizeResource::collection(Size::select('id', 'name', 'tag')->get());

            $healthConditions = HealthConditionResource::collection(HealthCondition::select('id', 'name', 'tag')->get());

            $data = [
                'species'             => $species,
                'genders'             => $genders,
                'sizes'               => $sizes,
                'health_conditions'   => $healthConditions,
            ];

            return $this->sendResponse(
                data: $data,
                message: 'Listado de catálogo para publicar mascota obtenido',
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener los catálogos: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo obtener los catálogos',
                error: $th->getMessage(),
            );
        }
    }
}