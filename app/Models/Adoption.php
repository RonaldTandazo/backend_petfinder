<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adoption extends Model
{
    protected $table = 'adoptions';

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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