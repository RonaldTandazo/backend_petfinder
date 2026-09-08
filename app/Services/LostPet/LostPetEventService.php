<?php

namespace App\Services\LostPet;

use App\Models\LostPetEvent;
use App\Services\Storage\PictureDeletionService;
use App\Services\Storage\PictureSyncService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ValidatedInput;

class LostPetEventService
{
    public function __construct(
        protected PictureDeletionService $pictureDeletionService,
        protected PictureSyncService $pictureSyncService
    ) {}

    public function getLostPetSightingsByLostPetId(int $lostPetId): Collection
    {
        $lostPetEvents = LostPetEvent::where('lost_pet_id', $lostPetId)
            ->where('lost_pet_event_type_id', 1)    
            ->orderByDesc('event_date')
            ->get();

        return $lostPetEvents;
    }

    public function create(ValidatedInput $validated, int $tutorId): LostPetEvent
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $lostPetEventData = $validated->except(['photos']);
            $lostPetEventData['tutor_id'] = $tutorId;
            
            $lostPetEvent = LostPetEvent::create($lostPetEventData);

            $photos = $validated['photos'] ?? [];

            if (!empty($photos)) {
                $photosToInsert = collect($photos)->map(function ($photo) use ($tutorId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $tutorId,
                    ];
                })->toArray();

                $pictures = $lostPetEvent->pictures()->createMany($photosToInsert);

                $this->pictureSyncService->syncMany($pictures);
            }

            return $lostPetEvent;
        });
    }

    public function delete(int $lostPetEventId, int $tutorId): void
    {
        DB::transaction(function () use ($lostPetEventId, $tutorId) {
            $lostPetEvent = LostPetEvent::where('id', $lostPetEventId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $lostPetEvent->pictures->each(fn ($picture) => $this->pictureDeletionService->delete($picture));
            $lostPetEvent->pictures()->delete();

            $lostPetEvent->delete();
        });
    }
}
