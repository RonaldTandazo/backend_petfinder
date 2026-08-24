<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HealthCondition extends Model
{
    protected $table = 'health_conditions';

    protected $fillable = ['name', 'tag', 'description'];

    protected $hidden = ['created_at', 'updated_at'];

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(
            Pet::class,
            'pet_health_conditions',
            'health_condition_id',
            'pet_id'
        );
    }
}
