<?php

namespace App\Models\Community;

use MongoDB\Laravel\Eloquent\Model;

class Comment extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'community_comments';

    protected $fillable = [
        'post_id',
        'parent_id',
        'author',
        'content',
    ];
}
