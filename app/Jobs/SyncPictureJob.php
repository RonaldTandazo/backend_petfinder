<?php

namespace App\Jobs;

use App\Models\Picture;
use App\Services\Storage\PictureSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SyncPictureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    protected EloquentCollection $pictures;

    public function __construct(Picture|array|Collection $pictures)
    {
        $this->pictures = match (true) {
            $pictures instanceof Picture => new EloquentCollection([$pictures]),
            $pictures instanceof EloquentCollection => $pictures,
            default => new EloquentCollection(collect($pictures)->all()),
        };
    }

    public function handle(PictureSyncService $pictureSyncService): void
    {
        $pictureSyncService->syncMany($this->pictures);
    }
}
