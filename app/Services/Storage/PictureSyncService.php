<?php

namespace App\Services\Storage;

use App\Models\Picture;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PictureSyncService
{
    public function syncMany(Collection $pictures): void
    {
        $pictures->each(fn (Picture $picture) => $this->syncOne($picture));
    }


    public function syncOne(Picture $picture): void
    {
        if ($picture->synced || !$picture->path_temp) {
            return;
        }

        $tempDisk = Storage::disk('s3_temp');

        $sourceBucket = config('filesystems.disks.s3_temp.bucket');
        $targetBucket = config('filesystems.disks.s3.bucket');

        $cleanTempKey = basename(parse_url($picture->path_temp, PHP_URL_PATH));
        $targetKey    = ltrim($picture->path ?? $this->buildKey($picture), '/');

        $copySource = '/' . $sourceBucket . '/' . rawurlencode($cleanTempKey);

        if (!$tempDisk->exists($cleanTempKey)) return;

        Storage::disk('s3')->getClient()->copyObject([
            'Bucket'            => $targetBucket,
            'Key'               => $targetKey,
            'CopySource'        => $copySource,
            'MetadataDirective' => 'REPLACE',
            'Metadata'          => [
                'picture_id'       => (string) $picture->id,
                'pictureable_id'   => (string) $picture->pictureable_id,
                'pictureable_type' => class_basename($picture->pictureable_type),
                'tutor_id'         => (string) ($picture->uploaded_by_id ?? ''),
            ],
        ]);

        Storage::disk('s3_temp')->delete($cleanTempKey);

        $picture->update([
            'path'   => $targetKey,
            'synced' => true,
        ]);
    }

    protected function buildKey(Picture $picture): string
    {
        $extension = pathinfo($picture->path_temp, PATHINFO_EXTENSION);
        $type      = Str::plural(Str::snake(class_basename($picture->pictureable_type)));
        $year      = now()->year;

        return "{$type}/{$year}/" . Str::uuid() . ".{$extension}";
    }
}
