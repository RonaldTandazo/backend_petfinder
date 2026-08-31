<?php

namespace App\Models;

use App\Models\Catalog\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Shelter extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'shelters';

    protected $fillable = [
        'name',
        'business_name',
        'tax_identification',
        'email',
        'password',
        'telephone',
        'physical_address',
        'country_id',
        'city',
        'latitude',
        'longitude',
        'web_page',
        'business_hours',
        'logo',
        'verified',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'verified' => 'boolean',
            'latitude' => 'double',
            'longitude' => 'double',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class);
    }
}