<?php

namespace App\Models\Management;

use App\Models\Account\Tutor;
use App\Models\Catalog\DeletionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountDeletion extends Model
{
    protected $table = 'account_deletions';

    protected $fillable = [
        'tutor_id',
        'requested_at',
        'schedule_deletion_at',
        'deletion_status_id',
        'cancelled_at',
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'schedule_deletion_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime'
            
        ];
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function deletionStatus(): BelongsTo
    {
        return $this->belongsTo(DeletionStatus::class);
    }
}
