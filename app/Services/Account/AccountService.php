<?php

namespace App\Services\Account;

use App\Helpers\ValidationErrorHelper;
use App\Models\Adoption;
use App\Models\LostPet;
use App\Models\LostPetFollows;
use App\Models\Pet;
use App\Models\PetFollows;
use App\Services\Storage\PictureSyncService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ValidatedInput;

class AccountService
{
    public function __construct(
        protected PictureSyncService $pictureSyncService
    ) {}

    public function getProfileMetrics(int $tutorId): array
    {
        $postsCount     = Pet::where('tutor_id', $tutorId)->count() + LostPet::where('tutor_id', $tutorId)->count();
        $adoptionsCount = Adoption::where('tutor_id', $tutorId)->where('adoption_status_id', 3)->count();
        $followsCount   = PetFollows::where('tutor_id', $tutorId)->count() + LostPetFollows::where('tutor_id', $tutorId)->count();
        
        return [
            [
                'label'  => 'Publicaciones',
                'count'  => $postsCount
            ],
            [
                'label'  => 'Adopciones',
                'count'  => $adoptionsCount
            ],
            [
                'label'  => 'Favoritos',
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

            $pictures = $account->avatar()->delete();
    
            $photos = $validated['avatar'] ?? [];
    
            if (!empty($photos)) {
                $photosToInsert = collect($photos)->map(function ($photo) use ($account) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $account->tutor->id,
                    ];
                })->toArray();
    
                $pictures = $account->avatar()->createMany($photosToInsert);
    
                $this->pictureSyncService->syncMany($pictures);
            }
    
            return $account;
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