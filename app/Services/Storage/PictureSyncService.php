<?php

namespace App\Services\Storage;

use App\Models\Picture\Picture;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PictureSyncService
{
    private const DISK = 's3_temp';
    
    public function __construct(protected MediaSyncService $mediaSyncService) {}

    public function syncMany(Collection $pictures): void
    {
        $pictures->each(fn (Picture $picture) => $this->syncOne($picture));
    }


    public function syncOne(Picture $picture): void
    {
        if ($picture->synced || !$picture->path_temp) {
            return;
        }

        $tempDisk = Storage::disk(self::DISK);

        $cleanTempKey = basename(parse_url($picture->path_temp, PHP_URL_PATH));
        
        if (!$tempDisk->exists($cleanTempKey)) return;

        $targetKey = $picture->path ?? $this->buildKey($picture);

        $this->mediaSyncService->copyToPermanentBucket($picture->path_temp, $targetKey, [
            'picture_id'       => (string) $picture->id,
            'pictureable_id'   => (string) $picture->pictureable_id,
            'pictureable_type' => class_basename($picture->pictureable_type),
            'tutor_id'       => (string) ($picture->uploaded_by_id ?? ''),
        ]);

        Storage::disk(self::DISK)->delete($cleanTempKey);

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
