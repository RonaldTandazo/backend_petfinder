<?php

namespace App\Services\Management;

use App\Helpers\ValidationErrorHelper;
use App\Models\Management\AccountDeletion;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ValidatedInput;

class ManagementService
{
    private const PENDING_STATUS_ID = 1;
    private const IN_PROGRESS_STATUS_ID = 2;

    public function changePassword(Authenticatable $account, ValidatedInput $validated): void
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

    public function existsAccountDeletion(int $tutorId): bool{
        $pendingDeletion = AccountDeletion::where('tutor_id', $tutorId)
            ->whereIn('deletion_status_id', [self::PENDING_STATUS_ID, self::IN_PROGRESS_STATUS_ID])
            ->exists();

        return $pendingDeletion;
    }

    public function deleteAccount(int $tutorId): AccountDeletion
    {
        return DB::transaction(function () use ($tutorId) {
            $requestedAt = now();

            $deletion = AccountDeletion::create([
                'tutor_id'             => $tutorId,
                'requested_at'         => $requestedAt,
                'schedule_deletion_at' => $requestedAt->copy()->addDays(15),
                'deletion_status_id'   => self::PENDING_STATUS_ID,
                'cancelled_at'         => null,
                'completed_at'         => null,
            ]);

            return $deletion;
        });
    }

    public function cancelAccountDeletion(int $tutorId): void{
        AccountDeletion::where('tutor_id', $tutorId)
            ->whereIn('deletion_status_id', [self::PENDING_STATUS_ID])
            ->update([
                'deletion_status_id' => 3,
                'cancelled_at' => now(),
            ]);
    }
}