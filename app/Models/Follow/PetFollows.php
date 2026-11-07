<?php

namespace App\Models\Follow;

use App\Models\Account\Tutor;
use App\Models\LostPet\LostPet;
use App\Models\Pet\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PetFollows extends Model
{
    protected $table = 'pet_follows';

    protected $fillable = [
        'pet_id',
        'pet_type',
        'tutor_id'
    ];

    public function pet(): MorphTo
    {
        return $this->morphTo();
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}
