<?php

namespace App\Models;

use App\Models\Catalog\AdoptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionEvent extends Model
{
    protected $table = 'adoption_events';

    protected $fillable = [
        'adoption_id',
        'tutor_id',
        'adoption_status_id',
        'comment',
    ];

    public function adoption(): BelongsTo
    {
        return $this->belongsTo(Adoption::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AdoptionStatus::class, 'adoption_status_id');
    }
}