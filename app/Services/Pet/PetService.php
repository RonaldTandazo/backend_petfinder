<?php

namespace App\Services\Pet;

use App\Jobs\SyncPictureJob;
use App\Models\Pet;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PetService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}


    public function list(int $tutorId, int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $pets = Pet::where('tutor_id', $tutorId)
            ->select(['id', 'name', 'species_id', 'animal_gender_id', 'size_id', 'born_date'])
            ->with(['mainPicture', 'animalGender', 'species', 'size'])
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

    public function find(int $petId, int $tutorId): Pet
    {
        return Pet::where('id', $petId)
            ->where('tutor_id', $tutorId)
            ->with(['pictures'])
            ->firstOrFail();
    }

    public function create(array $validated, int $tutorId, int $userId): Pet
    {
        return DB::transaction(function () use ($validated, $tutorId, $userId) {
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
                $photosToInsert = collect($photos)->map(function ($photo) use ($userId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $userId,
                    ];
                })->toArray();

                $pictures = $pet->pictures()->createMany($photosToInsert);

                SyncPictureJob::dispatch($pictures)->afterCommit();
            }

            return $pet;
        });
    }

    public function update(int $petId, int $tutorId, int $userId, array $validated): Pet
    {
        return DB::transaction(function () use ($petId, $tutorId, $userId, $validated) {
            $pet = Pet::where('id', $petId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $petData = array_diff_key($validated, ['photos' => '']);

            $pet->update($petData);

            if (isset($validated['photos'])) {
                $pet->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
                $pet->pictures()->delete();

                if (!empty($validated['photos'])) {
                    $photosToInsert = collect($validated['photos'])->map(function ($photo) use ($userId) {
                        return [
                            'path_temp'      => $photo['path_temp'],
                            'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                            'uploaded_by_id' => $userId,
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
}
