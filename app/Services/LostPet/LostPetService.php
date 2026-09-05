<?php

namespace App\Services\LostPet;

use App\Jobs\SyncPictureJob;
use App\Models\LostPet;
use App\Models\LostPetFollows;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;

class LostPetService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function getLostPets(int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $lostPets = LostPet::where('report_status_id', 1)
            ->where('report_type_id', 1)
            ->select(['id', 'name', 'race', 'species_id', 'animal_gender_id', 'city', 'event_address', 'longitude', 'latitude', 'event_date', 'report_status_id'])
            ->with(['animalGender', 'species', 'reportStatus'])
            ->latest()
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $lostPets->count() > $limit;

        return [
            'items'   => $lostPets->take($limit),
            'hasMore' => $hasMore,
        ];
    }

    public function getLostPetById(int $lostPetId): LostPet
    {
        $lostPet = LostPet::where('id', $lostPetId)
            ->with(['reportType', 'species', 'animalGender', 'size', 'reportStatus'])
            ->firstOrFail();

        return $lostPet;
    }

    public function create(array $validated, int $tutorId): LostPet
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $lostPetData = collect($validated)->except(['photos'])->toArray();
            $lostPetData['tutor_id'] = $tutorId;

            $lostPet = LostPet::create($lostPetData);

            $photos           = $validated['photos'] ?? [];

            if (!empty($photos)) {
                $photosToInsert = collect($photos)->map(function ($photo) use ($tutorId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $tutorId,
                    ];
                })->toArray();

                $pictures = $lostPet->pictures()->createMany($photosToInsert);

                SyncPictureJob::dispatch($pictures)->afterCommit();
            }

            return $lostPet;
        });
    }

    public function update(int $lostPetId, int $tutorId, array $validated): LostPet
    {
        return DB::transaction(function () use ($lostPetId, $tutorId, $validated) {
            $lostPet = LostPet::where('id', $lostPetId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $lostPetData = array_diff_key($validated, ['photos' => '']);

            $lostPet->update($lostPetData);

            if (isset($validated['photos'])) {
                $lostPet->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
                $lostPet->pictures()->delete();

                if (!empty($validated['photos'])) {
                    $photosToInsert = collect($validated['photos'])->map(function ($photo) use ($tutorId) {
                        return [
                            'path_temp'      => $photo['path_temp'],
                            'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                            'uploaded_by_id' => $tutorId,
                        ];
                    })->toArray();

                    $pictures = $lostPet->pictures()->createMany($photosToInsert);

                    SyncPictureJob::dispatch($pictures)->afterCommit();
                }
            }

            return $lostPet;
        });
    }

    public function delete(int $lostPetId, int $tutorId): void
    {
        DB::transaction(function () use ($lostPetId, $tutorId) {
            $lostPet = LostPet::where('id', $lostPetId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $lostPet->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
            $lostPet->pictures()->delete();

            $lostPet->delete();
        });
    }

    public function getLostPetFollowState(int $lostPetId, int $tutorId): bool
    {        
        return LostPetFollows::where('lost_pet_id', $lostPetId)
            ->where('tutor_id', $tutorId)
            ->exists();
    }

    public function handleFollow(int $lostPetId, int $tutorId, bool $followStatus): void
    {
        if ($followStatus) {
            LostPetFollows::create([
                'lost_pet_id' => $lostPetId,
                'tutor_id'    => $tutorId,
            ]);
        } else {
            LostPetFollows::where('lost_pet_id', $lostPetId)
                ->where('tutor_id', $tutorId)
                ->delete();
        }
    }
}
