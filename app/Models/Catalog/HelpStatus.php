<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class HelpStatus extends Model
{
    protected $table = 'help_statuses';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];
}