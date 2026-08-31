<?php

namespace App\Models;

use App\Models\Catalog\LostPetEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LostPetEvent extends Model
{
    protected $table = 'lost_pet_events';

    protected $fillable = [
        'lost_pet_id',
        'user_id',
        'lost_pet_event_type_id',
        'description',
        'address',
        'latitude',
        'longitude',
        'event_date',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'latitude'   => 'double',
            'longitude'  => 'double',
        ];
    }

    public function lostPet(): BelongsTo
    {
        return $this->belongsTo(LostPet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(LostPetEventType::class, 'lost_pet_event_type_id');
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }
}