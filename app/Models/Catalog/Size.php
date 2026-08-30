<?php

namespace App\Models\Catalog;

use App\Models\LostPet;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $table = 'sizes';

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