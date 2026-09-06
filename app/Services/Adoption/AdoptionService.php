<?php

namespace App\Services\Adoption;

use App\Jobs\SyncPictureJob;
use App\Models\Pet;
use App\Models\PetFollows;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ValidatedInput;

class AdoptionService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function getAdoptionPets(array $filters, int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $pets = Pet::where('pet_status_id', 1)
            ->with(['species', 'animalGender', 'size', 'healthConditions'])
            ->when(!empty($filters['tutor_id']), function ($q) use ($filters) {
                $q->where('tutor_id', $filters['tutor_id']);
            })
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $searchTerm = '%' . trim($filters['search']) . '%';

                $q->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'ILIKE', $searchTerm)
                        ->orWhere('race', 'ILIKE', $searchTerm)
                        ->orWhere('color', 'ILIKE', $searchTerm);
                });
            })
            ->when(!empty($filters['species']), function ($q) use ($filters) {
                $q->whereIn('species_id', $filters['species']);
            })
            ->when(!empty($filters['genders']), function ($q) use ($filters) {
                $q->whereIn('animal_gender_id', $filters['genders']);
            })
            ->when(!empty($filters['sizes']), function ($q) use ($filters) {
                $q->whereIn('size_id', $filters['sizes']);
            })
            ->when(!empty($filters['health_conditions']), function ($q) use ($filters) {
                $q->whereHas('healthConditions', function ($hq) use ($filters) {
                    $hq->whereIn('pet_health_conditions.health_condition_id', $filters['health_conditions']);
                });
            })
            ->orderByDesc('is_urgent')
            ->orderByDesc('created_at')
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $pets->count() > $limit;

        return [
            'items'   => $pets->take($limit),
            'hasMore' => $hasMore,
        ];
    }

    public function getAdoptionPetById(int $petId): Pet
    {
        $pet = Pet::where('id', $petId)
            ->with(['species', 'animalGender', 'size', 'healthConditions.healthCondition', 'petStatus'])
            ->firstOrFail();

        return $pet;
    }

    public function create(ValidatedInput $validated, int $tutorId): Pet
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $petData = $validated->except(['photos', 'health_conditions']);
            $petData['tutor_id'] = $tutorId;

            $pet = Pet::create($petData);

            $photos           = $validated['photos'] ?? [];
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
                $photosToInsert = collect($photos)->map(function ($photo) use ($tutorId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $tutorId,
                    ];
                })->toArray();

                $pictures = $pet->pictures()->createMany($photosToInsert);

                SyncPictureJob::dispatch($pictures)->afterCommit();
            }

            return $pet;
        });
    }

    public function update(int $petId, int $tutorId, ValidatedInput $validated): Pet
    {
        return DB::transaction(function () use ($petId, $tutorId, $validated) {
            $pet = Pet::where('id', $petId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $petData = $validated->except(['photos', 'health_conditions']);

            $pet->update($petData);

            if (isset($validated['photos'])) {
                $pet->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
                $pet->pictures()->delete();

                if (!empty($validated['photos'])) {
                    $photosToInsert = collect($validated['photos'])->map(function ($photo) use ($tutorId) {
                        return [
                            'path_temp'      => $photo['path_temp'],
                            'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                            'uploaded_by_id' => $tutorId,
                        ];
                    })->toArray();

                    $pictures = $pet->pictures()->createMany($photosToInsert);

                    SyncPictureJob::dispatch($pictures)->afterCommit();
                }
            }

            return $pet;
        });
    }

    public function delete(int $petId, int $tutorId): void
    {
        DB::transaction(function () use ($petId, $tutorId) {
            $pet = Pet::where('id', $petId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $pet->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
            $pet->pictures()->delete();

            $pet->delete();
        });
    }

    public function getAdoptionPetFollowState(int $petId, int $tutorId): bool
    {        
        return PetFollows::where('pet_id', $petId)
            ->where('tutor_id', $tutorId)
            ->exists();
    }

    public function handleFollow(int $petId, int $tutorId, bool $followStatus): void
    {
        if ($followStatus) {
            PetFollows::create([
                'pet_id'   => $petId,
                'tutor_id' => $tutorId,
            ]);
        } else {
            PetFollows::where('pet_id', $petId)
                ->where('tutor_id', $tutorId)
                ->delete();
        }
    }
}
