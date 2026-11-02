<?php

namespace App\Services\Password;

use App\Models\Account\Shelter;
use App\Models\Account\User;
use App\Models\Auth\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\ValidatedInput;

class PasswordService
{
    private function findAccountsByEmail(string $email): array
    {
        return [
            'user'    => User::where('email', $email)->first(),
            'shelter' => Shelter::where('email', $email)->first(),
        ];
    }

    public function forgotPassword(ValidatedInput $credentials): array
    {
        $email        = $credentials['email'];
        $selectedType = $credentials['account_type'] ?? null;

        $accounts = $this->findAccountsByEmail($email);

        $user     = $accounts['user'];
        $shelter  = $accounts['shelter'];

        if (!$user && !$shelter) {
            return ['status' => 'not_found', 'payload' => null];
        }

        if (!$selectedType && $user && $shelter) {
            return [
                'status' => 'select_account',
                'payload' => [
                    'requires_account_selection' => true
                ],
            ];
        }

        if ($selectedType) {
            $targetAccount = $selectedType === 'user' ? $user : $shelter;

            if (!$targetAccount) {
                return [
                    'status' => 'not_found',
                    'payload' => null,
                ];
            }

            $accountType = $selectedType;
        } else {
            $targetAccount = $user ?? $shelter;
            $accountType = $user ? 'user' : 'shelter';
        }

        return [
            'status'  => 'found',
            'payload' => [
                'account'      => $targetAccount,
                'account_type' => $accountType,
            ],
        ];
    }

    public function generatePasswordResetToken(string $email, string $accountType): array {
        $token = Str::random(64);

        PasswordResetToken::updateOrInsert(
            [
                'email' => $email,
                'account_type' => $accountType,
            ],
            [
                'token' => hash('sha256', $token),
                'expires_at' => now()->addHour(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $url = route('password.reset.form', [
            'token' => $token,
            'email' => $email,
            'account_type' => $accountType,
        ]);

        return [
            'token' => $token,
            'url' => $url,
        ];
    }

    public function validatePasswordResetToken(string $email, string $accountType, string $token): bool {
        $reset = PasswordResetToken::query()
            ->where('email', $email)
            ->where('account_type', $accountType)
            ->first();

        if (!$reset) {
            return false;
        }

        return hash_equals(
            $reset->token,
            hash('sha256', $token)
        ) && now()->lessThan($reset->expires_at);
    }

    public function resetPassword(string $email, string $accountType, string $token, string $password): bool {
        return DB::transaction(function () use ($email, $accountType, $token, $password) {
            $reset = PasswordResetToken::query()
                ->where('email', $email)
                ->where('account_type', $accountType)
                ->lockForUpdate()
                ->first();

            if (!$reset) {
                return false;
            }

            $tokenHash = hash('sha256', $token);

            if (
                !hash_equals($reset->token, $tokenHash) ||
                now()->greaterThanOrEqualTo($reset->expires_at)
            ) {
                return false;
            }

            // Obtener la cuenta según su tipo.
            $account = match ($accountType) {
                'user' => User::where('email', $email)->first(),
                'shelter' => Shelter::where('email', $email)->first(),
                default => null,
            };

            if (!$account) {
                return false;
            }

            $account->password = Hash::make($password);
            $account->save();

            $reset->delete();

            return true;
        });
    }
}
