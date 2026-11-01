<?php

namespace App\Services\Legal;

use App\Models\Legal\LegalDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LegalDocumentVersionService
{
    public function publishMany(array $documents): array
    {
        return DB::transaction(function () use ($documents) {

            $publishedVersions = [];

            foreach ($documents as $data) {

                $document = LegalDocument::query()
                    ->where('tag', $data['tag'])
                    ->lockForUpdate()
                    ->first();

                if (!$document) {
                    throw ValidationException::withMessages([
                        'tag' => "El documento {$data['tag']} no existe.",
                    ]);
                }

                $versionExists = $document->versions()
                    ->where('version', $data['version'])
                    ->exists();

                if ($versionExists) {
                    throw ValidationException::withMessages([
                        'version' => "La versión {$data['version']} ya existe para {$document->name}.",
                    ]);
                }

                $version = $document->versions()->create([
                    'version' => $data['version'],
                    'md_path' => $data['paths']['md_path'],
                    'pdf_path' => $data['paths']['pdf_path'],
                    'published_at' => now(),
                ]);

                $document->update([
                    'current_version_id' => $version->id,
                ]);

                $publishedVersions[] = $version;
            }

            return $publishedVersions;
        });
    }
}
