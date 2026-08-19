<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalGender extends Model
{
    protected $table = 'animal_genders';

    protected $fillable = ['name', 'tag'];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}