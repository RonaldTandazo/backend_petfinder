<?php

namespace App\Models;

use App\Models\Catalog\Country;
use App\Models\Catalog\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_names',
        'last_names',
        'email',
        'password',
        'phone_mobile',
        'country_id',
        'gender_id',
        'city',
        'address',
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->first_names) . " ". trim($this->last_names)
        );
    }

    public function avatar(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')->where('is_main', true);
    }
}