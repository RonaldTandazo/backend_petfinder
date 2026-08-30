<?php

namespace App\Services\Community;

use App\Models\Community\Post;
use App\Services\Storage\MediaSyncService;
use Illuminate\Support\Str;

class PostImageSyncService
{
    public function __construct(protected MediaSyncService $mediaSyncService) {}

    public function sync(Post $post): void
    {
        $images = $post->images ?? [];

        $changed = false;

        foreach ($images as &$image) {
            if (($image['synced'] ?? false) || empty($image['path_temp'])) {
                continue;
            }

            $targetKey = $image['path'] ?? $this->buildKey($image['path_temp']);

            $this->mediaSyncService->copyToPermanentBucket($image['path_temp'], $targetKey, [
                'id_post'  => (string) $post->id,
                'id_tutor' => (string) ($post->author['tutor_id'] ?? ''),
            ]);

            $image['path']   = $targetKey;
            $image['synced'] = true;

            $changed = true;
        }

        unset($image);

        if ($changed) {
            $post->update(['images' => $images]);
        }
    }

    protected function buildKey(string $pathTemp): string
    {
        $extension = pathinfo($pathTemp, PATHINFO_EXTENSION);

        return 'community/' . now()->year . '/' . Str::uuid() . ".{$extension}";
    }
}
