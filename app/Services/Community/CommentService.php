<?php

namespace App\Services\Community;

use App\Models\Community\Comment;
use App\Models\Community\Post;

class CommentService
{
    public function store(string $postId, string $content, array $author): Comment
    {
        Post::where('_id', $postId)
            ->where('status', 'published')
            ->firstOrFail();

        $comment = Comment::create([
            'post_id' => $postId,
            'author'  => $author,
            'content' => $content,
        ]);

        $this->touchCount($postId, 1);

        return $comment;
    }

    public function list(string $postId, int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $comments = Comment::where('post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $comments->count() > $limit;

        $items = $comments->take($limit)->map(fn (Comment $comment) => [
            'id'         => (string) $comment->id,
            'author'     => $comment->author,
            'content'    => $comment->content,
            'created_at' => $comment->created_at?->toIso8601String(),
        ]);

        return [
            'items'   => $items,
            'hasMore' => $hasMore,
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

        $comment->delete();

        $this->touchCount($comment->post_id, -1);
    }

    public function touchCount(string $postId, int $delta): void
    {
        Post::where('_id', $postId)->increment('comments_count', $delta);
    }
}
