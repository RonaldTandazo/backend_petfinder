<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LostPetEventType extends Model
{
    protected $table = 'lost_pet_event_types';

    protected $fillable = ['name', 'tag'];

    public function events(): HasMany
    {
        return $this->hasMany(LostPetEvent::class);
    }
}