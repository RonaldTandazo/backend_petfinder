<?php

namespace App\Models\Community;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'community_posts';

    protected $fillable = [
        'author',
        'news_type',
        'title',
        'content',
        'images',
        'status',
        'reactions',
        'reactions_count',
        'comments_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'reactions_count' => 'int',
            'comments_count'  => 'int',
            'published_at'    => 'datetime',
        ];
    }
}
