<?php

namespace App\Services\Legal;

use App\Models\Legal\LegalDocumentAcceptances;
use Illuminate\Support\Facades\DB;

class LegalDocumentAcceptanceService
{
    public function store(array $acceptances)
    {
        return DB::transaction(function () use ($acceptances) {
            return LegalDocumentAcceptances::insert($acceptances);
        });
    }
}
