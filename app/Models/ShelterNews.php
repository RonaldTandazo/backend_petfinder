<?php

namespace App\Models;

use App\Models\Catalog\NewsType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ShelterNews extends Model
{
    protected $table = 'shelter_news';

    protected $fillable = [
        'shelter_id',
        'title',
        'content',
        'news_type_id',
    ];

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function newsType(): BelongsTo
    {
        return $this->belongsTo(NewsType::class);
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }
}