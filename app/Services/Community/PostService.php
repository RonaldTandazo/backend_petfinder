<?php

namespace App\Services\Community;

use App\Jobs\SyncPostImageJob;
use App\Models\Catalog\NewsType;
use App\Models\Community\Comment;
use App\Models\Community\Post;
use App\Services\Storage\PurgeS3ObjectService;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function __construct(protected PurgeS3ObjectService $purgeS3ObjectService) {}

    public function publish(array $validated, array $author): Post
    {
        $post = Post::create([
            'author'          => $author,
            'news_type'       => $this->newsTypeSnapshot($validated['news_type_id']),
            'title'           => $validated['title'],
            'content'         => $validated['content'],
            'images'          => $this->toImageDocuments($validated['images']),
            'status'          => 'published',
            'reactions'       => [],
            'reactions_count' => 0,
            'comments_count'  => 0,
            'published_at'    => now(),
        ]);

        SyncPostImageJob::dispatch($post)->afterCommit();

        return $post;
    }

    public function list(int $page, int $limit, ?int $tutorId): array
    {
        $skip  = ($page - 1) * $limit;

        $posts = Post::where('status', 'published')
            ->orderByDesc('published_at')
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $posts->count() > $limit;
        $posts   = $posts->take($limit);

        $reactedIds = $tutorId
            ? Post::whereIn('_id', $posts->pluck('id'))
                ->where('reactions.tutor_id', $tutorId)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all()
            : [];

        $items = $posts->map(fn (Post $post) => $this->mapPost(
            $post,
            in_array((string) $post->id, $reactedIds, true)
        ));

        return [
            'items'   => $items,
            'hasMore' => $hasMore,
        ];
    }

    public function show(string $postId, ?int $tutorId): array
    {
        $post = Post::where('_id', $postId)
            ->where('status', 'published')
            ->firstOrFail();

        $reactedByMe = $tutorId
            && Post::where('_id', $postId)
                ->where('reactions.tutor_id', $tutorId)
                ->exists();

        return $this->mapPost($post, $reactedByMe);
    }

    public function update(string $postId, int $tutorId, array $validated): Post
    {
        $post = $this->findOwned($postId, $tutorId);

        $post->update([
            'title'     => $validated['title'] ?? $post->title,
            'content'   => $validated['content'] ?? $post->content,
            'news_type' => isset($validated['news_type_id'])
                ? $this->newsTypeSnapshot($validated['news_type_id'])
                : $post->news_type,
        ]);

        return $post->refresh();
    }

    public function delete(string $postId, int $tutorId): void
    {
        $post = $this->findOwned($postId, $tutorId);

        $this->purgeImage($post);

        Comment::where('post_id', $post->id)->delete();

        $post->delete();
    }

    protected function findOwned(string $postId, int $tutorId): Post
    {
        return Post::where('_id', $postId)
            ->where('author.tutor_id', $tutorId)
            ->firstOrFail();
    }

    protected function newsTypeSnapshot(int $id): array
    {
        $newsType = NewsType::findOrFail($id);

        return ['id' => $newsType->id, 'name' => $newsType->name, 'tag' => $newsType->tag];
    }

    protected function mapPost(Post $post, bool $reactedByMe): array
    {
        return [
            'id'              => (string) $post->id,
            'author'          => $post->author,
            'news_type'       => $post->news_type,
            'title'           => $post->title,
            'content'         => $post->content,
            'images'          => $this->imageUrls($post),
            'reactions_count' => $post->reactions_count,
            'comments_count'  => $post->comments_count,
            'reacted_by_me'   => $reactedByMe,
            'created_at'      => $post->created_at?->toIso8601String(),
        ];
    }

    protected function toImageDocuments(array $pathTemps): array
    {
        return array_map(
            static fn (string $pathTemp) => [
                'path_temp' => $pathTemp,
                'path'      => null,
                'synced'    => false,
            ],
            $pathTemps
        );
    }

    protected function imageUrls(Post $post): array
    {
        return array_map(
            fn (array $image) => $this->imageUrl($image),
            $post->images ?? []
        );
    }

    protected function imageUrl(?array $image): ?string
    {
        if (empty($image)) return null;
        
        if (($image['synced'] ?? false) && $image['path']) {
            return Storage::disk('s3')->url($image['path']);
        }

        if ($image['path_temp']) {
            return Storage::disk('s3_temp')->temporaryUrl($image['path_temp'], now()->addMinutes(15));
        }

        return null;
    }

    protected function purgeImage(Post $post): void
    {
        foreach ($post->images ?? [] as $image) {
            if (! empty($image['path'])) {
                $this->purgeS3ObjectService->purgeAllVersions($image['path']);
            }

            if (! empty($image['path_temp']) && empty($image['synced'])) {
                Storage::disk('s3_temp')->delete($image['path_temp']);
            }
        }
    }
}
