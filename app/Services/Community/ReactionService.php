<?php

namespace App\Services\Community;

use App\Models\Community\Post;
use MongoDB\BSON\ObjectId;

class ReactionService
{
    public const TYPE = 'heart';

    public function toggle(string $postId, array $author): array
    {
        Post::where('_id', $postId)
            ->where('status', 'published')
            ->firstOrFail();

        $active = !$this->alreadyReacted($postId, $author['tutor_id']);

        $this->applyReaction($postId, $author, $active);

        return [
            'active'          => $active,
            'reactions_count' => $this->count($postId),
        ];
    }

    public function unreact(string $postId, array $author): array
    {
        Post::where('_id', $postId)
            ->where('status', 'published')
            ->firstOrFail();

        $this->applyReaction($postId, $author, false);

        return [
            'active'          => false,
            'reactions_count' => $this->count($postId),
        ];
    }

    protected function alreadyReacted(string $postId, int $tutorId): bool
    {
        return Post::where('_id', $postId)
            ->where('reactions.tutor_id', $tutorId)
            ->where('reactions.type', self::TYPE)
            ->exists();
    }

    protected function applyReaction(string $postId, array $author, bool $active): void
    {
        Post::where('_id', $postId)->raw(function ($collection) use ($postId, $author, $active) {
            $operator = $active ? '$addToSet' : '$pull';

            $payload = $active
                ? [
                    'tutor_id'   => $author['tutor_id'],
                    'tutor_type' => $author['tutor_type'],
                    'type'       => self::TYPE,
                    'created_at' => now()->toIso8601String(),
                ]
                : [
                    'tutor_id' => $author['tutor_id'],
                    'type'     => self::TYPE,
                ];

            $collection->updateOne(
                ['_id' => new ObjectId($postId)],
                [
                    $operator => ['reactions' => $payload],
                    '$inc'    => ['reactions_count' => $active ? 1 : -1],
                ]
            );
        });
    }

    protected function count(string $postId): int
    {
        return max(0, (int) (Post::where('_id', $postId)->first()?->reactions_count ?? 0));
    }
}
