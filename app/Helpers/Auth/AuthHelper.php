<?php

namespace App\Helpers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Resources\ShelterResource;
use App\Models\User;
use App\Models\Shelter;
use Illuminate\Support\Facades\Log;

class AuthHelper
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
        $token = $account->createToken($tokenName)->plainTextToken;

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
}