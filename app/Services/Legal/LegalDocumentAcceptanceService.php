<?php

namespace App\Services\Legal;

use App\Models\Legal\LegalDocumentAcceptance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LegalDocumentAcceptanceService
{
    public function acceptDocuments(array $acceptances)
    {
        return DB::transaction(function () use ($acceptances) {
            return LegalDocumentAcceptance::insert($acceptances);
        });
    }

    public function getPendingAcceptances(array $legalDocuments, int $tutorId): array
    {
        $acceptedVersionIds = LegalDocumentAcceptance::where('tutor_id', $tutorId)
            ->pluck('legal_document_version_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $pending = [];

        foreach ($legalDocuments as $tag => $detail) {
            $versionId = $detail['version_id'];

            if (
                $versionId !== null &&
                !in_array((int) $versionId, $acceptedVersionIds, true)
            ) {
                $pending[] = [
                    'id'           => $detail['document_id'],
                    'name'         => $detail['document'],
                    'tag'          => $tag,
                    'version'      => $detail['version'],
                    'path'         => $detail['path'],
                    'published_at' => $detail['published_at']
                ];
            }
        }

        return $pending;
    }

    public function acceptPendingDocuments(array $legalDocuments, int $tutorId, array $tags): void {
        $pending = $this->getPendingAcceptances($legalDocuments, $tutorId);

        $pendingTags = array_column($pending, 'tag');

        $submittedTags = array_values(array_unique($tags));

        sort($pendingTags);
        sort($submittedTags);

        if ($pendingTags !== $submittedTags) {
            throw ValidationException::withMessages([
                'accepted_documents' => 'Los documentos enviados no coinciden con los documentos pendientes de aceptación. Actualiza la pantalla e inténtalo nuevamente',
            ]);
        }

        $now = now();

        $acceptances = [];

        foreach ($pending as $document) {
            $versionId = $legalDocuments[$document['tag']]['version_id'];

            $acceptances[] = [
                'tutor_id'                  => $tutorId,
                'legal_document_version_id' => $versionId,
                'accepted_at'               => $now,
                'created_at'                => $now,
                'updated_at'                => $now,
            ];
        }

        if (!empty($acceptances)) {
            $this->acceptDocuments($acceptances);
        }
    }
}
