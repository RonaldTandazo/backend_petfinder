<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends Model
{
    protected $table = 'species';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function lostPets(): HasMany
    {
        return $this->hasMany(LostPet::class);
    }
}