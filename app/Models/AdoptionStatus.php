<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdoptionStatus extends Model
{
    protected $table = 'adoption_statuses';

    protected $fillable = ['name', 'tag'];

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }
}