<?php

namespace App\Models;

use App\Models\Catalog\AdoptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Adoption extends Model
{
    protected $table = 'adoptions';

    protected $fillable = [
        'tutor_id',
        'pet_id',
        'adoption_status_id',
        'application_date',
        'closing_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'application_date' => 'datetime',
            'closing_date' => 'datetime',
        ];
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AdoptionStatus::class, 'adoption_status_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AdoptionEvent::class)->orderBy('created_at', 'asc');
    }
}