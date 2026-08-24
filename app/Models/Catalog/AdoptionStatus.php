<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdoptionStatus extends Model
{
    protected $table = 'adoption_statuses';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];

    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }
}