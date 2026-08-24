<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportStatus extends Model
{
    protected $table = 'report_statuses';

    protected $fillable = ['name', 'tag'];

    protected $hidden = ['created_at', 'updated_at'];

    public function lostPets(): HasMany
    {
        return $this->hasMany(LostPet::class);
    }
}