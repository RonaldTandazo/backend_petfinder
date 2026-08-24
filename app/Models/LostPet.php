<?php

namespace App\Models;

use App\Models\Catalog\ReportStatus;
use App\Models\Catalog\ReportType;
use App\Models\Catalog\Species;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LostPet extends Model
{
    protected $table = 'lost_pets';

    protected $fillable = [
        'tutor_id',
        'pet_id',
        'report_type_id',
        'name',
        'species_id',
        'race',
        'color',
        'description',
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
            'event_date' => 'datetime',
            'closing_date' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function reportType(): BelongsTo
    {
        return $this->belongsTo(ReportType::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class);
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(LostPetPicture::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(LostPetEvent::class);
    }
}