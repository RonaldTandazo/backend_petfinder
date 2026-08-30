<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;

class PurgeS3ObjectService
{
    public function purgeAllVersions(string $key): void
    {
        $client = Storage::disk('s3')->getClient();
        $bucket = config('filesystems.disks.s3.bucket');

        $versions = $client->listObjectVersions([
            'Bucket' => $bucket,
            'Prefix' => $key,
        ]);

        $objects = collect($versions['Versions'] ?? [])
            ->merge($versions['DeleteMarkers'] ?? [])
            ->filter(fn ($version) => $version['Key'] === $key)
            ->map(fn ($version) => ['Key' => $key, 'VersionId' => $version['VersionId']])
            ->values()
            ->all();

        if (empty($objects)) {
            return;
        }

        $client->deleteObjects([
            'Bucket' => $bucket,
            'Delete' => ['Objects' => $objects],
        ]);
    }
}
