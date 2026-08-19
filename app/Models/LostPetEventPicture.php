<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostPetEventPicture extends Model
{
    protected $table = 'lost_pet_event_pictures';

    protected $fillable = ['lost_pet_event_id', 'picture'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(LostPetEvent::class, 'lost_pet_event_id');
    }
}