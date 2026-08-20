<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_names',
        'last_names',
        'username',
        'email',
        'password',
        'telephone',
        'country_id',
        'gender_id',
        'city',
        'address',
        'avatar',
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

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    public function lostPetEvents(): HasMany
    {
        return $this->hasMany(LostPetEvent::class);
    }
}