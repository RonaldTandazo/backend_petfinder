<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorType extends Model
{
    protected $table = 'tutor_types';

    protected $fillable = ['name', 'tag'];

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class);
    }
}