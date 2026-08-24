<?php

namespace App\Http\Controllers\Pet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pet\FormPetRequest;
use App\Http\Resources\PetListResource;
use App\Http\Resources\PetDetailResource;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class PetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $page = max(1, $request->integer('page', 1));
            $limit = min(50, max(1, $request->integer('limit', 20)));
            $skip = ($page - 1) * $limit;

            $pets = Pet::where('tutor_id', $tutorId)
                ->select(['id', 'name', 'species_id', 'animal_gender_id', 'size_id', 'born_date'])
                ->with(['mainPicture', 'animalGender', 'species', 'size'])
                ->latest()
                ->skip($skip)
                ->take($limit + 1)
                ->get();

            $hasMore = $pets->count() > $limit;
            $petList = PetListResource::collection($pets->take($limit));

            $data = [
                'hasMore' => $hasMore,
                'pets'    => $petList,
            ];

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

    public function show(int $petId): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $pet = Pet::where('id', $petId)
                ->where('tutor_id', $tutorId)
                ->with(['pictures'])
                ->firstOrFail();

            return $this->sendResponse(
                data: $pet,
                message: 'Información de la mascota obtenida exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message: 'Mascota no encontrada',
                code: Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener detalle de la mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo obtener el detalle de la mascota',
                error: $th->getMessage()
            );
        }
    }

    public function store(FormPetRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $validated = $request->validated();

                $petData = collect($validated)->except(['photos', 'health_conditions'])->toArray();
                $petData['tutor_id'] = $this->getTutorId();

                $pet = Pet::create($petData);

                $photos = $validated['photos'] ?? [];
                $healthConditions = $validated['health_conditions'] ?? [];

                if (!empty($healthConditions)) {
                    $healthConditions = collect($healthConditions)->map(function ($healthCondition) {
                        return [
                            'health_condition_id' => $healthCondition
                        ];
                    })->toArray();

                    $pet->healthConditions()->createMany($healthConditions);
                }

                if (!empty($photos)) {
                    $photosToInsert = collect($photos)->map(function ($photo) {
                        return [
                            'picture' => $photo['path'],
                            'is_main' => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        ];
                    })->toArray();

                    $pet->pictures()->createMany($photosToInsert);
                }

                return [
                    'pet_id' => $pet->id
                ];
            });

            return $this->sendResponse(
                data: $result,
                message: 'Mascota registrada exitosamente',
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $th) {
            Log::error('Error registrando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo completar el registro del mascota',
                error: $th->getMessage()
            );
        }
    }

    public function update(FormPetRequest $request, int $petId): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request, $petId) {
                $tutorId = $this->getTutorId();

                $pet = Pet::where('id', $petId)
                    ->where('tutor_id', $tutorId)
                    ->firstOrFail();

                $validated = $request->validated();
                $petData = array_diff_key($validated, ['photos' => '']);

                $pet->update($petData);

                if (isset($validated['photos'])) {
                    $pet->pictures()->delete();

                    if (!empty($validated['photos'])) {
                        $photosToInsert = collect($validated['photos'])->map(function ($photo) {
                            return [
                                'picture' => $photo['path'],
                                'is_main' => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                            ];
                        })->toArray();

                        $pet->pictures()->createMany($photosToInsert);
                    }
                }

                return [
                    'pet_id' => $pet->id
                ];
            });

            return $this->sendResponse(
                data: $result,
                message: 'Mascota actualizada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message: 'Mascota no encontrada',
                code: Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo actualizar mascota',
                error: $th->getMessage()
            );
        }
    }

    public function delete(int $petId): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($petId) {
                $tutorId = $this->getTutorId();

                $pet = Pet::where('id', $petId)
                    ->where('tutor_id', $tutorId)
                    ->firstOrFail();

                $pet->pictures()->delete();

                $pet->delete();
            });

            return $this->sendResponse(
                message: 'Mascota eliminada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message: 'Mascota no encontrada',
                code: Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando mascota: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'No se pudo eliminar mascota',
                error: $th->getMessage()
            );
        }
    }
}