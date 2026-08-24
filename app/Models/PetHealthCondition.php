<?php

namespace App\Models;

use App\Models\Catalog\HealthCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetHealthCondition extends Model
{
    protected $table = 'pet_health_conditions';

    protected $fillable = [
        'pet_id',
        'health_condition_id'
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function healthCondition(): BelongsTo
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }
}
