<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class DeletionStatus extends Model
{
    protected $table = 'deletion_statuses';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];
}
