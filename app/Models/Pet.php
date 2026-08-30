<?php

namespace App\Models;

use App\Models\Catalog\AnimalGender;
use App\Models\Catalog\PetStatus;
use App\Models\Catalog\Size;
use App\Models\Catalog\Species;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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
        'is_urgent',
        'animal_gender_id',
        'size_id',
        'description',
        'pet_status_id',
    ];

    protected function casts(): array
    {
        return [
            'born_date' => 'date',
            'is_urgent' => 'boolean',
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

    public function healthConditions(): HasMany
    {
        return $this->hasMany(PetHealthCondition::class);
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    public function lostPetReports(): HasMany
    {
        return $this->hasMany(LostPet::class);
    }

    public function mainPicture(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')->where('is_main', true);
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->born_date) {
                    return null;
                }

                $bornDate = Carbon::parse($this->born_date);
                $diff = $bornDate->diff(Carbon::now());

                $years = $diff->y;
                $months = $diff->m;

                if ($years === 0 && $months === 0) {
                    $label = 'Recién nacido';
                } else {
                    $parts = [];
                    if ($years > 0) {
                        $parts[] = "{$years} " . ($years === 1 ? 'año' : 'años');
                    }
                    if ($months > 0) {
                        $parts[] = "{$months} " . ($months === 1 ? 'mes' : 'meses');
                    }
                    $label = implode(' y ', $parts);
                }

                return [
                    'years'  => $years,
                    'months' => $months,
                    'label'  => $label,
                ];
            }
        );
    }
}