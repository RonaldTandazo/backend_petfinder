<?php

namespace App\Http\Controllers\Adoption;

use App\Http\Controllers\Controller;
use App\Http\Resources\Adoption\AdoptionPetResource;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdoptionController extends Controller
{
    public function getAdoptionPets(Request $request): JsonResponse
    {
        try {
            $page = max(1, $request->integer('page', 1));
            $limit = min(50, max(1, $request->integer('limit', 20)));
            $skip = ($page - 1) * $limit;

            $pets = Pet::where('pet_status_id', 1)
                ->select(['id', 'name', 'species_id', 'animal_gender_id', 'race', 'born_date'])
                ->with(['animalGender', 'species'])
                ->latest()
                ->skip($skip)
                ->take($limit + 1)
                ->get();

            $hasMore = $pets->count() > $limit;
            $petList = AdoptionPetResource::collection($pets->take($limit));

            $data = [
                'hasMore' => $hasMore,
                'pets'    => $petList,
            ];

            Log::alert($data);

            return $this->sendResponse(
                data: $data,
                message: 'Listado de mascotas obtenido exitosamente',
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener lista de mascotas: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo obtener el listado de mascotas',
                error: $th->getMessage(),
            );
        }
    }
}