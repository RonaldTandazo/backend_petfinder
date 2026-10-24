<?php

namespace App\Models\Help;

use App\Models\Catalog\HelpStatus;
use App\Models\Picture;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Support extends Model
{
    protected $table = 'supports';

    protected $fillable = [
        'tutor_id',
        'subject',
        'comment',
        'help_status_id'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable')
            ->orderByDesc('is_main');
    }

    public function helpStatus(): BelongsTo
    {
        return $this->belongsTo(HelpStatus::class);
    }
}
