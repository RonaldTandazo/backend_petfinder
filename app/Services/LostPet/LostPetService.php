<?php

namespace App\Services\LostPet;

use App\Jobs\SyncPictureJob;
use App\Models\LostPet;
use App\Models\LostPetFollows;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ValidatedInput;

class LostPetService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function getLostPets(array $filters, int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $lostPets = LostPet::with(['species', 'animalGender', 'reportStatus'])
            ->whereNull('closing_date')
            ->where('report_status_id', 1)
            ->where('report_type_id', 1)
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
            ->orderByDesc('event_date')
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
            ->with(['species', 'animalGender', 'size', 'reportType', 'reportStatus'])
            ->firstOrFail();

        return $lostPet;
    }

    public function create(ValidatedInput $validated, int $tutorId): LostPet
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $lostPetData = $validated->except(['photos']);
            $lostPetData['tutor_id'] = $tutorId;

            $lostPet = LostPet::create($lostPetData);

            $photos = $validated['photos'] ?? [];

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

    public function update(int $lostPetId, int $tutorId, ValidatedInput $validated): LostPet
    {
        return DB::transaction(function () use ($lostPetId, $tutorId, $validated) {
            $lostPet = LostPet::where('id', $lostPetId)
                ->where('tutor_id', $tutorId)
                ->firstOrFail();

            $lostPetData = $validated->except(['photos']);

            $lostPet->update($lostPetData);

            if (isset($validated['photos'])) {
                $photos = collect($validated['photos']);

                $existingPictures = $lostPet->pictures()->get()->keyBy('id');

                $receivedExistingIds = $photos->where('type', 'existing')->pluck('id')->map(fn ($id) => (int) $id)->values();

                $invalidIds = $receivedExistingIds->filter(fn ($id) => !$existingPictures->has($id));

                if ($invalidIds->isNotEmpty()) {
                    throw new \InvalidArgumentException(
                        'Una o más imágenes no pertenecen a esta publicación.'
                    );
                }

                $picturesToDelete = $existingPictures->filter(fn ($picture) => !$receivedExistingIds->contains($picture->id));

                foreach ($picturesToDelete as $picture) {
                    $this->pictureDeletionService->delete($picture);
                    $picture->delete();
                }

                foreach ($photos->where('type', 'existing') as $photo) {
                    $picture = $existingPictures->get((int) $photo['id']);

                    if (!$picture) continue;

                    $picture->update([
                        'is_main'        => filter_var(
                            $photo['is_main'],
                            FILTER_VALIDATE_BOOLEAN
                        ),
                        'uploaded_by_id' => $tutorId,
                    ]);
                }

                $newPhotos = $photos->where('type', 'new');

                if ($newPhotos->isNotEmpty()) {
                    $photosToInsert = $newPhotos->map(function ($photo) use ($tutorId) {
                        return [
                            'path_temp'      => $photo['path_temp'],
                            'is_main'        => filter_var(
                                $photo['is_main'],
                                FILTER_VALIDATE_BOOLEAN
                            ),
                            'uploaded_by_id' => $tutorId,
                        ];
                    })->values()->toArray();

                    $pictures = $lostPet->pictures()->createMany($photosToInsert);

                    SyncPictureJob::dispatch($pictures)->afterCommit();
                }
            }

            return $lostPet->fresh(['pictures']);
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
