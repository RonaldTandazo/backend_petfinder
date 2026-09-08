<?php

namespace App\Services\Account;

use App\Helpers\ValidationErrorHelper;
use App\Models\Adoption;
use App\Models\LostPet;
use App\Models\LostPetFollows;
use App\Models\Pet;
use App\Models\PetFollows;
use App\Models\Shelter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class AccountService
{
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

    public function updateProfile(Authenticatable $account, array $validated): Authenticatable
    {
        $type   = $account instanceof Shelter ? 'shelter' : 'user';
        $fields = array_intersect_key($validated, ProfileFields::fieldsFor($type));

        if ($fields) {
            $account->fill($fields)->save();
        }

        $account->load($type === 'user' ? ['country', 'gender', 'tutor'] : ['country', 'tutor']);

        return $account;
    }

    public function updatePassword(Authenticatable $account, array $validated): void
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