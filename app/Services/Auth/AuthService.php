<?php

namespace App\Services\Auth;

use App\Http\Resources\ShelterResource;
use App\Http\Resources\UserResource;
use App\Models\Catalog\TutorType;
use App\Models\Shelter;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function findAccountsByEmail(string $email): array
    {
        return [
            'user'    => User::where('email', $email)->first(),
            'shelter' => Shelter::where('email', $email)->first(),
        ];
    }

    public function generateAuthPayload($account, string $type): array
    {
        $account->tokens()->delete();

        $tokenName = "{$type}_auth_token";
        $token     = $account->createToken($tokenName)->plainTextToken;

        if ($type === 'user') {
            $account->load(['country', 'gender', 'tutor']);
            $resource = new UserResource($account);
        } else {
            $account->load(['country', 'tutor']);
            $resource = new ShelterResource($account);
        }

        return [
            'type'         => $type,
            'account'      => $resource,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }

    public function registerUser(array $validated): User
    {
        return DB::transaction(function () use ($validated) {
            $user = User::create($validated);

            $userTutorType = TutorType::where('tag', 'USER')->first();

            Tutor::create([
                'tutor_type_id' => $userTutorType?->id ?? 1,
                'user_id'       => $user->id,
                'shelter_id'    => null,
            ]);

            return $user;
        });
    }

    public function registerShelter(array $validated): Shelter
    {
        return DB::transaction(function () use ($validated) {
            $shelterData = array_merge($validated, [
                'verified' => false,
            ]);

            $shelter = Shelter::create($shelterData);

            $shelterTutorType = TutorType::where('tag', 'SHELTER')->first();

            Tutor::create([
                'tutor_type_id' => $shelterTutorType?->id ?? 2,
                'user_id'       => null,
                'shelter_id'    => $shelter->id,
            ]);

            return $shelter;
        });
    }

    public function login(array $credentials): array
    {
        $email        = $credentials['email'];
        $password     = $credentials['password'];
        $selectedType = $credentials['account_type'] ?? null;

        $accounts = $this->findAccountsByEmail($email);
        $user     = $accounts['user'];
        $shelter  = $accounts['shelter'];

        if (!$user && !$shelter) {
            return ['status' => 'invalid_credentials', 'payload' => null];
        }

        if ($selectedType) {
            $targetAccount = $selectedType === 'user' ? $user : $shelter;

            if (!$targetAccount || !Hash::check($password, $targetAccount->password)) {
                return ['status' => 'invalid_credentials', 'payload' => null];
            }

            return [
                'status'  => 'success',
                'payload' => $this->generateAuthPayload($targetAccount, $selectedType),
            ];
        }

        $userValid    = $user && Hash::check($password, $user->password);
        $shelterValid = $shelter && Hash::check($password, $shelter->password);

        if (!$userValid && !$shelterValid) {
            return ['status' => 'invalid_credentials', 'payload' => null];
        }

        if ($userValid && $shelterValid) {
            return [
                'status'  => 'select_account',
                'payload' => [
                    'requires_account_selection' => true,
                    'available_accounts'         => ['user', 'shelter'],
                ],
            ];
        }

        $activeAccount = $userValid ? $user : $shelter;
        $type          = $userValid ? 'user' : 'shelter';

        return [
            'status'  => 'success',
            'payload' => $this->generateAuthPayload($activeAccount, $type),
        ];
    }

    public function sessionInfo(Authenticatable $account, bool $isUser): array
    {
        return [
            'type'    => $isUser ? 'user' : 'shelter',
            'account' => $account,
        ];
    }

    public function logout(Authenticatable $account): void
    {
        $account->currentAccessToken()->delete();
    }
}
