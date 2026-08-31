<?php

namespace App\Models;

use App\Models\Catalog\AnimalGender;
use App\Models\Catalog\ReportStatus;
use App\Models\Catalog\ReportType;
use App\Models\Catalog\Size;
use App\Models\Catalog\Species;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LostPet extends Model
{
    protected $table = 'lost_pets';

    protected $fillable = [
        'tutor_id',
        'name',
        'race',
        'color',
        'description',
        'telephone',
        'report_type_id',
        'species_id',
        'animal_gender_id',
        'size_id',
        'has_reward',
        'reward_amount',
        'city',
        'event_address',
        'latitude',
        'longitude',
        'event_date',
        'report_status_id',
        'closing_date',
    ];

    protected function casts(): array
    {
        return [
            'has_reward'    => 'boolean',
            'reward_amount' => 'double',
            'event_date'    => 'datetime',
            'closing_date'  => 'datetime',
            'latitude'      => 'double',
            'longitude'     => 'double',
        ];
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class);
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

    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class);
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LostPetEvent::class);
    }

    public function mainPicture(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')->where('is_main', true);
    }
}