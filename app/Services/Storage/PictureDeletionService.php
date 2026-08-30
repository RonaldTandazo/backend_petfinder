<?php

namespace App\Services\Storage;

use App\Models\Picture;
use Illuminate\Support\Facades\Storage;

class PictureDeletionService
{
    public function __construct(protected PurgeS3ObjectService $purgeS3ObjectService) {}

    public function delete(Picture $picture): void
    {
        if ($picture->path) {
            $this->purgeS3ObjectService->purgeAllVersions($picture->path);
        }

        if ($picture->path_temp) {
            Storage::disk('s3_temp')->delete($picture->path_temp);
        }

        $picture->update([
            'path' => null,
            'path_temp' => null,
            'synced' => false,
        ]);
    }
}
