<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocument extends Model
{
    protected $table = 'legal_documents';

    protected $fillable = [
        'name',
        'tag',
        'current_version_id',
    ];


    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(LegalDocumentVersion::class, 'current_version_id');
    }
}
