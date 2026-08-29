<?php

namespace App\Services\Picture;

use App\Helpers\ValidationErrorHelper;
use App\Jobs\SyncPictureJob;
use App\Models\Pet;
use App\Models\Picture;
use App\Services\Storage\PictureDeletionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PictureService
{
    public function __construct(protected PictureDeletionService $pictureDeletionService) {}

    /**
     * Reemplaza el binario de una o varias pictures existentes manteniendo su path.
     */
    public function updateMany(array $items, int $tutorId, int $userId): void
    {
        $pictures = DB::transaction(function () use ($items, $tutorId, $userId) {
            $updated = new Collection();

            foreach ($items as $item) {
                $picture = $this->findOwned((int) $item['id'], $tutorId);

                $picture->update([
                    'path_temp'      => $item['path_temp'],
                    'synced'         => false,
                    'uploaded_by_id' => $userId,
                ]);

                $updated->push($picture);
            }

            return $updated;
        });

        SyncPictureJob::dispatch($pictures)->afterCommit();
    }

    public function delete(int $pictureId, int $tutorId): void
    {
        $picture = $this->findOwned($pictureId, $tutorId);

        $this->pictureDeletionService->delete($picture);
    }

    public function resolveUrl(int $pictureId, bool $temp, int $tutorId): string
    {
        if ($temp) {
            $picture = $this->findOwned($pictureId, $tutorId);

            if (!$picture->path_temp) {
                ValidationErrorHelper::throwValidationError('Esta foto no tiene un archivo temporal pendiente.');
            }

            return Storage::disk('s3_temp')->temporaryUrl($picture->path_temp, now()->addMinutes(15));
        }

        $picture = Picture::findOrFail($pictureId);

        if (!$picture->path) {
            ValidationErrorHelper::throwValidationError('Esta foto todavía no está disponible en su ubicación definitiva.');
        }

        return Storage::disk('s3')->url($picture->path);
    }


    protected function findOwned(int $pictureId, int $tutorId): Picture
    {
        return Picture::whereHasMorph('pictureable', [Pet::class], function ($query) use ($tutorId) {
            $query->where('tutor_id', $tutorId);
        })->findOrFail($pictureId);
    }
}
