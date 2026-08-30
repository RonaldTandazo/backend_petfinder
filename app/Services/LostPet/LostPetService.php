<?php

namespace App\Services\LostPet;

use App\Jobs\SyncPictureJob;
use App\Models\LostPet;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;

class LostPetService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

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
}
