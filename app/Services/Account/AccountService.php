<?php

namespace App\Services\Account;

use App\Helpers\ValidationErrorHelper;
use App\Models\Adoption\Adoption;
use App\Models\LostPet\LostPet;
use App\Models\LostPet\LostPetFollows;
use App\Models\Pet\Pet;
use App\Models\Pet\PetFollows;
use App\Services\Storage\PictureDeletionService;
use App\Services\Storage\PictureSyncService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ValidatedInput;

class AccountService
{
    public function __construct(
        protected PictureSyncService $pictureSyncService,
        protected PictureDeletionService $pictureDeletionService
    ) {}

    public function getProfileMetrics(int $tutorId): array
    {
        $postsCount     = Pet::where('tutor_id', $tutorId)->count() + LostPet::where('tutor_id', $tutorId)->count();
        $adoptionsCount = Adoption::where('tutor_id', $tutorId)->where('adoption_status_id', 3)->count();
        $followsCount   = PetFollows::where('tutor_id', $tutorId)->count() + LostPetFollows::where('tutor_id', $tutorId)->count();
        
        return [
            [
                'label'  => 'Publicaciones',
                'tag'    => 'POSTINGS',
                'count'  => $postsCount
            ],
            [
                'label'  => 'Adopciones',
                'tag'    => 'ADOPTIONS',
                'count'  => $adoptionsCount
            ],
            [
                'label'  => 'Favoritos',
                'tag'    => 'FAVOURITES',
                'count'  => $followsCount,
                'action' => 'open_followed'
            ]
        ];
    }

    public function updateProfile(Authenticatable $account, ValidatedInput $validated): Authenticatable
    {
        return DB::transaction(function () use ($validated, $account) {
            $fields = $validated->except(['avatar']);

            $account->fill($fields)->save();

            if (isset($validated['avatar'])) {
                $photos = collect($validated['avatar']);

                $existingPictures = $account->avatar()->get()->keyBy('id');

                $receivedExistingIds = $photos->where('type', 'existing')->pluck('id')->map(fn ($id) => (int) $id)->values();

                $invalidIds = $receivedExistingIds->filter(fn ($id) => !$existingPictures->has($id));

                if ($invalidIds->isNotEmpty()) {
                    throw new \InvalidArgumentException(
                        'Una o más imágenes no pertenecen a este perfil.'
                    );
                }

                $picturesToDelete = $existingPictures
                    ->filter(
                        fn ($picture) =>
                            !$receivedExistingIds->contains($picture->id)
                    );

                foreach ($picturesToDelete as $picture) {
                    $this->pictureDeletionService->delete($picture);
                    $picture->delete();
                }

                foreach ($photos->where('type', 'existing') as $photo) {
                    $picture = $existingPictures->get((int) $photo['id']);

                    if (!$picture) {
                        continue;
                    }

                    $picture->update([
                        'is_main' => filter_var(
                            $photo['is_main'],
                            FILTER_VALIDATE_BOOLEAN
                        ),
                        'uploaded_by_id' => $account->tutor->id,
                    ]);
                }

                $newPhotos = $photos->where('type', 'new');

                if ($newPhotos->isNotEmpty()) {
                    $photosToInsert = $newPhotos
                        ->map(function ($photo) use ($account) {
                            return [
                                'path_temp' => $photo['path_temp'],
                                'is_main' => filter_var(
                                    $photo['is_main'],
                                    FILTER_VALIDATE_BOOLEAN
                                ),
                                'uploaded_by_id' => $account->tutor->id,
                            ];
                        })
                        ->values()
                        ->toArray();

                    $pictures = $account->avatar()
                        ->createMany($photosToInsert);

                    $this->pictureSyncService->syncMany($pictures);
                }
            }

            return $account->fresh(['avatar']);
        });
    }

    public function updatePassword(Authenticatable $account, ValidatedInput $validated): void
    {
        if (!Hash::check($validated['current_password'], $account->password)) {
            ValidationErrorHelper::throwValidationError([
                'current_password' => 'La contraseña actual no es correcta',
            ]);
        }

        $account->fill(['password' => $validated['new_password']])->save();

        $currentTokenId = $account->currentAccessToken()?->id;

        $account->tokens()
            ->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))
            ->delete();
    }
}