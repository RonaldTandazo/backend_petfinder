<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorType extends Model
{
    protected $table = 'tutor_types';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class);
    }
}