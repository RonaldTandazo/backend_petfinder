<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetPicture extends Model
{
    protected $table = 'pet_pictures';

    protected $fillable = ['pet_id', 'picture', 'is_main'];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}