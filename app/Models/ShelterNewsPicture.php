<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShelterNewsPicture extends Model
{
    protected $table = 'shelter_news_pictures';

    protected $fillable = ['shelter_news_id', 'picture'];

    public function news(): BelongsTo
    {
        return $this->belongsTo(ShelterNews::class, 'shelter_news_id');
    }
}