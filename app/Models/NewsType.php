<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsType extends Model
{
    protected $table = 'news_types';

    protected $fillable = ['name', 'tag'];

    public function shelterNews(): HasMany
    {
        return $this->hasMany(ShelterNews::class);
    }
}