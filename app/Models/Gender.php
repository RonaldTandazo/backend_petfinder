<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends Model
{
    protected $table = 'genders';

    protected $fillable = ['name', 'tag'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
