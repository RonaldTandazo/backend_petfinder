<?php

namespace App\Services\Storage;

use App\Models\Picture;
use Illuminate\Support\Facades\Storage;

class PictureDeletionService
{

    public function delete(Picture $picture): void
    {
        if ($picture->path) {
            $this->purgeAllVersions($picture->path);
        }

        if ($picture->path_temp) {
            Storage::disk('s3_temp')->delete($picture->path_temp);
        }

        $picture->update([
            'path'      => null,
            'path_temp' => null,
            'synced'    => false,
        ]);
    }

    protected function purgeAllVersions(string $key): void
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
