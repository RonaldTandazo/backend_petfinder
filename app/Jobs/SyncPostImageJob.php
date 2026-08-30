<?php

namespace App\Jobs;

use App\Models\Community\Post;
use App\Services\Community\PostImageSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPostImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Post $post) {}

    public function handle(PostImageSyncService $postImageSyncService): void
    {
        $postImageSyncService->sync($this->post);
    }
}
