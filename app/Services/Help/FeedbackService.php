<?php

namespace App\Services\Help;

use App\Jobs\SyncPictureJob;
use App\Models\Help\Feedback;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ValidatedInput;

class FeedbackService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function create(ValidatedInput $validated, int $tutorId): Feedback
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $feedbackData = $validated->except(['photos']);
            $feedbackData['tutor_id'] = $tutorId;

            $feedback = Feedback::create($feedbackData);

            $photos = $validated['photos'] ?? [];

            if (!empty($photos)) {
                $photosToInsert = collect($photos)->map(function ($photo) use ($tutorId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $tutorId,
                    ];
                })->toArray();

                $pictures = $feedback->pictures()->createMany($photosToInsert);

                SyncPictureJob::dispatch($pictures)->afterCommit();
            }

            return $feedback;
        });
    }
}
