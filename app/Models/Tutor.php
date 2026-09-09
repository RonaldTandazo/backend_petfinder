<?php

namespace App\Models;

use App\Models\Catalog\TutorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    protected $table = 'tutors';

    protected $fillable = [
        'tutor_type_id',
        'user_id',
        'shelter_id',
    ];

    public function tutorType(): BelongsTo
    {
        return $this->belongsTo(TutorType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    public function lostPets(): HasMany
    {
        return $this->hasMany(LostPet::class);
    }

    public function lostPetEvents(): HasMany
    {
        return $this->hasMany(LostPetEvent::class);
    }
}