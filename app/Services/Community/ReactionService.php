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
        $objectId = new ObjectId($postId);

        $payload = $active
            ? [
                'tutor_id'   => $author['tutor_id'],
                'tutor_type' => $author['tutor_type'],
                'type'       => self::TYPE,
            ]
            : [
                'tutor_id' => $author['tutor_id'],
                'type'     => self::TYPE,
            ];

        Post::where('_id', $postId)->raw(function ($collection) use ($objectId, $payload, $active) {
            $collection->updateOne(
                ['_id' => $objectId],
                [
                    $active ? '$addToSet' : '$pull' => ['reactions' => $payload],
                ]
            );

            $this->syncReactionCount($collection, $objectId);
        });
    }

    protected function syncReactionCount($collection, ObjectId $objectId): void
    {
        $doc = $collection->findOne(
            ['_id' => $objectId],
            ['projection' => ['reactions' => 1]]
        );

        $count = count($doc['reactions'] ?? []);

        $collection->updateOne(
            ['_id' => $objectId],
            ['$set' => ['reactions_count' => $count]]
        );
    }

    protected function count(string $postId): int
    {
        return max(0, (int) (Post::where('_id', $postId)->first()?->reactions_count ?? 0));
    }
}
