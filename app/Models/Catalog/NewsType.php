<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsType extends Model
{
    protected $table = 'news_types';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];

    public function shelterNews(): HasMany
    {
        return $this->hasMany(ShelterNews::class);
    }
}