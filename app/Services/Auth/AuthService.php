<?php

namespace App\Services\Auth;

use App\Http\Resources\Auth\SessionInfoResource;
use App\Models\Account\Shelter;
use App\Models\Account\Tutor;
use App\Models\Account\User;
use App\Models\Catalog\TutorType;
use App\Services\Management\ManagementService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(protected ManagementService $managementService) {}

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
        
        $isAccountDeletionActive = $credentials['is_account_deletion_active'] ?? false;
        $cancelAccountDeletion = $credentials['cancel_account_deletion'] ?? false;

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

            if($isAccountDeletionActive && $cancelAccountDeletion){
                $this->cancelAccountDeletion($targetAccount);
            }

            $existsDeletion = $this->validateAccountDeletion($targetAccount);
            if($existsDeletion){
                return [
                    'status'  => 'account_deletion',
                    'payload' => [
                        'exists_account_deletion' => true
                    ]
                ];
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

        if($isAccountDeletionActive && $cancelAccountDeletion){
            $this->cancelAccountDeletion($activeAccount);
        }

        $existsDeletion = $this->validateAccountDeletion($activeAccount);
        if($existsDeletion){
            return [
                'status'  => 'account_deletion',
                'payload' => [
                    'exists_account_deletion' => true
                ]
            ];
        }

        return [
            'status'  => 'success',
            'payload' => $this->generateAuthPayload($activeAccount, $type),
        ];
    }

    private function findAccountsByEmail(string $email): array
    {
        return [
            'user'    => User::where('email', $email)->first(),
            'shelter' => Shelter::where('email', $email)->first(),
        ];
    }

    private function generateAuthPayload($account, string $type): array
    {
        $account->tokens()->delete();

        $tokenName = "{$type}_auth_token";
        $token     = $account->createToken($tokenName)->plainTextToken;

        $session_info = $this->sessionInfo($account, $type === 'user');

        return [
            'session_info' => $session_info,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }

    public function sessionInfo(Authenticatable $account, bool $isUser): array
    {
        return SessionInfoResource::make($account)->toArray(request()) + [
            'is_shelter' => !$isUser,
            'is_user'    => $isUser,
        ];
    }

    private function validateAccountDeletion(Authenticatable $account): bool
    {
        $tutorId = $account->tutor?->id;

        return $this->managementService->existsAccountDeletion($tutorId);
    }

    private function cancelAccountDeletion(Authenticatable $account): void
    {
        $tutorId = $account->tutor?->id;

        $this->managementService->cancelAccountDeletion($tutorId);
    }

    public function logout(Authenticatable $account): void
    {
        $account->currentAccessToken()->delete();
    }
}
