<?php

namespace App\Services\Help;

use App\Jobs\SyncPictureJob;
use App\Models\Help\Support;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ValidatedInput;

class SupportService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    public function create(ValidatedInput $validated, int $tutorId): Support
    {
        return DB::transaction(function () use ($validated, $tutorId) {
            $supportData = $validated->except(['photos']);
            $supportData['tutor_id'] = $tutorId;

            $support = Support::create($supportData);

            $photos = $validated['photos'] ?? [];

            if (!empty($photos)) {
                $photosToInsert = collect($photos)->map(function ($photo) use ($tutorId) {
                    return [
                        'path_temp'      => $photo['path_temp'],
                        'is_main'        => filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN),
                        'uploaded_by_id' => $tutorId,
                    ];
                })->toArray();

                $pictures = $support->pictures()->createMany($photosToInsert);

                SyncPictureJob::dispatch($pictures)->afterCommit();
            }

            return $support;
        });
    }
}
