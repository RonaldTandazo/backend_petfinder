<?php

namespace App\Services\Legal;

use App\Models\Legal\LegalDocument;
use Illuminate\Support\Facades\Cache;

class LegalDocumentService
{
    private const CACHE_KEY = 'legal_documents.current_versions';

    private const CACHE_TTL = 1800;

    public function getCurrentVersions(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            function () {
                return LegalDocument::query()
                    ->with('currentVersion')
                    ->get()
                    ->mapWithKeys(function ($document) {
                        $version = $document->currentVersion;

                        return [
                            $document->tag => [
                                'document_id' => $document->id,
                                'version_id' => $version?->id,
                                'version' => $version?->version,
                                'path' => $version?->path,
                                'published_at' => $version?->published_at,
                            ],
                        ];
                    })
                    ->all();
            }
        );
    }

    public function getCurrentVersion(string $tag): ?array
    {
        $documents = $this->getCurrentVersions();

        return $documents[$tag] ?? null;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
