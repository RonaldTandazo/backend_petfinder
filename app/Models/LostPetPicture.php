<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostPetPicture extends Model
{
    protected $table = 'lost_pet_pictures';

    protected $fillable = ['lost_pet_id', 'picture'];

    public function lostPet(): BelongsTo
    {
        return $this->belongsTo(LostPet::class);
    }
}