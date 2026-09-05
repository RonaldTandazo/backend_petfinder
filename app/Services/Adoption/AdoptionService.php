<?php

namespace App\Services\Adoption;

use App\Jobs\SyncPictureJob;
use App\Models\Pet;
use App\Models\PetFollows;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;

class AdoptionService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function getAdoptionPets(int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $pets = Pet::where('pet_status_id', 1)
            ->with(['species', 'animalGender', 'size'])
            ->orderByDesc('is_urgent')
            ->latest()
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
        $lostPet = Pet::where('id', $petId)
            ->with(['species', 'animalGender', 'size', 'petStatus'])
            ->firstOrFail();

        return $lostPet;
    }

    public function create(array $validated, int $tutorId): Pet
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $petData = collect($validated)->except(['photos', 'health_conditions'])->toArray();
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

    public function update(int $petId, int $tutorId, array $validated): Pet
    {
        return DB::transaction(function () use ($petId, $tutorId, $validated) {
            $pet = Pet::where('id', $petId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $petData = array_diff_key($validated, ['photos' => '']);

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
