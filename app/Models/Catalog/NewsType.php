<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class NewsType extends Model
{
    protected $table = 'news_types';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];
}