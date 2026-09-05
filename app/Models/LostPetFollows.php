<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostPetFollows extends Model
{
    protected $table = 'lost_pet_follows';

    protected $fillable = [
        'lost_pet_id',
        'tutor_id'
    ];

    public function lostPet(): BelongsTo
    {
        return $this->belongsTo(LostPet::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}
