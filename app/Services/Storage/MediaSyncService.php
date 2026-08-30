<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;

class MediaSyncService
{

    public function copyToPermanentBucket(string $sourceKey, string $targetKey, array $metadata): void
    {
        $sourceBucket = config('filesystems.disks.s3_temp.bucket');
        $targetBucket = config('filesystems.disks.s3.bucket');

        Storage::disk('s3')->getClient()->copyObject([
            'Bucket'            => $targetBucket,
            'Key'               => $targetKey,
            'CopySource'        => $sourceBucket . '/' . rawurlencode($sourceKey),
            'MetadataDirective' => 'REPLACE',
            'Metadata'          => $metadata,
        ]);
    }
}
