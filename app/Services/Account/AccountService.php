<?php

namespace App\Services\Account;

use App\Helpers\ValidationErrorHelper;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\UserResource;
use App\Models\Catalog\Country;
use App\Models\Catalog\Gender;
use App\Models\Shelter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function formCatalog(Authenticatable $account): array
    {
        $account->load(
            $account instanceof Shelter ? ['country', 'tutor'] : ['country', 'gender', 'tutor']
        );

        return [
            'countries' => Country::query()->orderBy('name')->get(['id', 'name', 'abbreviation']),
            'genders'   => Gender::query()->orderBy('id')->get(['id', 'name', 'tag']),
            'me'        => $account instanceof Shelter
                ? new ShelterResource($account)
                : new UserResource($account),
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