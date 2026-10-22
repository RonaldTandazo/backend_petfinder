<?php

namespace App\Services\Legal;

use App\Models\Legal\LegalDocument;
use App\Models\Legal\LegalDocumentVersion;
use Illuminate\Support\Facades\DB;

class LegalDocumentVersionService
{
    public function publish(LegalDocument $document, array $data): LegalDocumentVersion {
        $version = DB::transaction(function () use ($document, $data) {
            $version = $document->versions()->create([
                'version' => $data['version'],
                'path' => $data['path'] ?? null,
                'published_at' => now(),
            ]);

            $document->update([
                'current_version_id' => $version->id,
            ]);

            return $version;
        });

        app(LegalDocumentService::class)->clearCache();

        return $version;
    }
}
