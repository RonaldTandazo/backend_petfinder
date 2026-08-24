<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = ['name', 'abbreviation'];

    protected $hidden = ['created_at', 'updated_at'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function shelters(): HasMany
    {
        return $this->hasMany(Shelter::class);
    }
}
