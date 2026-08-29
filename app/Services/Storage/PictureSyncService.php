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

        $sourceBucket = config('filesystems.disks.s3_temp.bucket');
        $targetBucket = config('filesystems.disks.s3.bucket');
        $targetKey    = $picture->path ?? $this->buildKey($picture);

        Storage::disk('s3')->getClient()->copyObject([
            'Bucket'            => $targetBucket,
            'Key'               => $targetKey,
            'CopySource'        => $sourceBucket . '/' . rawurlencode($picture->path_temp),
            'MetadataDirective' => 'REPLACE',
            'Metadata'          => [
                'id_picture'       => (string) $picture->id,
                'id_pictureable'   => (string) $picture->pictureable_id,
                'pictureable_type' => class_basename($picture->pictureable_type),
                'id_usuario'       => (string) ($picture->uploaded_by_id ?? ''),
            ],
        ]);

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
