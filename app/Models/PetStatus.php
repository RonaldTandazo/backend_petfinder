<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetStatus extends Model
{
    protected $table = 'pet_statuses';

    protected $fillable = ['name', 'tag'];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'pet_status_id');
    }
}