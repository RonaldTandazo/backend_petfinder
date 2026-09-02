<?php

namespace App\Services\Community;

use App\Helpers\ValidationErrorHelper;
use App\Models\Community\Comment;
use App\Models\Community\Post;

class CommentService
{
    public function store(string $postId, string $content, array $author, ?string $parentId = null): Comment
    {
        Post::where('_id', $postId)
            ->where('status', 'published')
            ->firstOrFail();

        if ($parentId !== null) {
            $this->assertValidParent($parentId, $postId);
        }

        $comment = Comment::create([
            'post_id'   => $postId,
            'parent_id' => $parentId,
            'author'    => $author,
            'content'   => $content,
        ]);

        // comments_count cuenta SOLO comentarios raíz (las respuestas no suman).
        $this->touchCount($postId, $parentId === null ? 1 : 0);

        return $comment;
    }

    public function list(string $postId, int $page, int $limit, ?string $parentId = null): array
    {
        $skip = ($page - 1) * $limit;

        $query = Comment::where('post_id', $postId);

        // validacion para traer al principio comentarios sin respuestas
        // luego solo traemos respuestas por el comentario padre
        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        $comments = $query
            ->orderBy('created_at', 'asc')
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $comments->count() > $limit;
        $items   = $comments->take($limit);

        $parentIds = $items
            ->pluck('parent_id')
            ->filter()
            ->unique()
            ->all();

        $parents = Comment::whereIn('_id', $parentIds)
            ->get()
            ->keyBy(fn (Comment $parent) => (string) $parent->id);

        $itemIds = $items
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $repliesCounts = Comment::where('post_id', $postId)
            ->whereIn('parent_id', $itemIds)
            ->pluck('parent_id')
            ->countBy()
            ->all();

        $mapped = $items->map(function (Comment $comment) use ($parents, $repliesCounts) {
            $parentId = $comment->parent_id ? (string) $comment->parent_id : null;
            $parent   = $parentId ? ($parents[$parentId] ?? null) : null;

            return $this->mapItem($comment, $parentId, $parent, $repliesCounts[(string) $comment->id] ?? 0);
        });

        return [
            'items'   => $mapped,
            'hasMore' => $hasMore,
        ];
    }

    public function single(string $commentId): array
    {
        $comment  = Comment::where('_id', $commentId)->firstOrFail();
        $parentId = $comment->parent_id ? (string) $comment->parent_id : null;
        $parent   = $parentId ? Comment::where('_id', $parentId)->first() : null;

        $repliesCount = Comment::where('post_id', $comment->post_id)
            ->where('parent_id', (string) $comment->id)
            ->count();

        return $this->mapItem($comment, $parentId, $parent, $repliesCount);
    }

    protected function mapItem(Comment $comment, ?string $parentId, ?Comment $parent, int $repliesCount): array
    {
        return [
            'id'            => (string) $comment->id,
            'author'        => $comment->author,
            'content'       => $comment->content,
            'parent_id'     => $parentId,
            'parent'        => $parent ? [
                'tutor_id'     => $parent->author['tutor_id'] ?? null,
                'display_name' => $parent->author['display_name'] ?? null,
            ] : null,
            'replies_count' => $repliesCount,
            'created_at'    => $comment->created_at?->toIso8601String(),
        ];
    }

    public function update(string $commentId, int $tutorId, string $content): Comment
    {
        $comment = Comment::where('_id', $commentId)
            ->where('author.tutor_id', $tutorId)
            ->firstOrFail();

        $comment->update(['content' => $content]);

        return $comment->refresh();
    }

    public function delete(string $commentId, int $tutorId): void
    {
        $comment = Comment::where('_id', $commentId)
            ->where('author.tutor_id', $tutorId)
            ->firstOrFail();

        $idsToDelete = [$commentId];
        $cursor      = [$commentId];

        while (!empty($cursor)) {
            $children = Comment::whereIn('parent_id', $cursor)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $cursor      = $children;
            $idsToDelete = array_merge($idsToDelete, $children);
        }

        Comment::whereIn('_id', $idsToDelete)->delete();

        // comments_count cuenta SOLO comentarios padre
        $this->touchCount($comment->post_id, $comment->parent_id ? 0 : -1);
    }

    public function touchCount(string $postId, int $delta): void
    {
        Post::where('_id', $postId)->increment('comments_count', $delta);
    }

    protected function assertValidParent(string $parentId, string $postId): void
    {
        $parent = Comment::where('_id', $parentId)->first();

        if (!$parent || (string) $parent->post_id !== $postId) {
            ValidationErrorHelper::throwValidationError([
                'parent_id' => 'El comentario al que respondes no pertenece a esta publicación',
            ]);
        }
    }
}
