<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Picture extends Model
{
    protected $table = 'pictures';

    protected $fillable = ['path', 'path_temp', 'is_main', 'synced', 'uploaded_by_id'];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'synced'  => 'boolean',
        ];
    }

    public function pictureable(): MorphTo
    {
        return $this->morphTo();
    }
}
