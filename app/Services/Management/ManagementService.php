<?php

namespace App\Services\Management;

use App\Models\Management\AccountDeletion;
use Illuminate\Support\Facades\DB;

class ManagementService
{
    private const PENDING_STATUS_ID = 1;

    public function existsPendingDeletion(int $tutorId): bool{
        $pendingDeletion = AccountDeletion::query()
            ->where('tutor_id', $tutorId)
            ->where('deletion_status_id', self::PENDING_STATUS_ID)
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
}