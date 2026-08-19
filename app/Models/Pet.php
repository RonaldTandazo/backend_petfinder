<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    protected $table = 'pets';

    protected $fillable = [
        'tutor_id',
        'name',
        'species_id',
        'race',
        'color',
        'born_date',
        'animal_gender_id',
        'size_id',
        'description',
        'pet_status_id',
    ];

    protected function casts(): array
    {
        return [
            'born_date' => 'date',
        ];
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function animalGender(): BelongsTo
    {
        return $this->belongsTo(AnimalGender::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function petStatus(): BelongsTo
    {
        return $this->belongsTo(PetStatus::class, 'pet_status_id');
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(PetPicture::class);
    }

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    public function lostPetReports(): HasMany
    {
        return $this->hasMany(LostPet::class);
    }
}