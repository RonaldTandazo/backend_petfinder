<?php

namespace App\Jobs\Management;

use App\Models\Account\Shelter;
use App\Models\Account\Tutor;
use App\Models\Account\User;
use App\Models\Management\AccountDeletion;
use App\Models\Picture\Picture;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DeleteAccountJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 300;

    public function __construct(
        private AccountDeletion $deletion
    ) {}

    public function handle(): void
    {
        $deletion = $this->deletion;

        // Verificar que la solicitud siga pendiente.
        if (
            (int) $deletion->deletion_status_id !== 1 ||
            $deletion->completed_at !== null ||
            $deletion->cancelled_at !== null
        ) {
            return;
        }

        // Verificar que haya llegado la fecha programada.
        if ($deletion->schedule_deletion_at->isFuture()) {
            return;
        }

        $tutorId = $deletion->tutor_id;

        if (!$tutorId) {
            throw new RuntimeException(
                'La solicitud de eliminación no tiene un tutor asociado.'
            );
        }

        $tutor = Tutor::find($tutorId);

        if (!$tutor) {
            throw new RuntimeException(
                "No se encontró el tutor {$tutorId} asociado a la solicitud."
            );
        }

        // Obtener las fotografías asociadas al tutor.
        $pictures = Picture::where('uploaded_by_id', $tutorId)->get();

        // Rutas permanentes.
        $permanentPaths = $pictures
            ->pluck('path')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Rutas temporales.
        $tempPaths = $pictures
            ->pluck('path_temp')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Eliminar fotografías permanentes.
        if (!empty($permanentPaths)) {
            $deleted = Storage::disk('s3')->delete($permanentPaths);

            if (!$deleted) {
                throw new RuntimeException(
                    'No se pudieron eliminar las fotografías permanentes.'
                );
            }
        }

        // Eliminar fotografías temporales.
        if (!empty($tempPaths)) {
            $deleted = Storage::disk('s3_temp')->delete($tempPaths);

            if (!$deleted) {
                throw new RuntimeException(
                    'No se pudieron eliminar las fotografías temporales.'
                );
            }
        }

        // Eliminar registros de la base de datos.
        DB::transaction(function () use ($tutor, $tutorId, $deletion) {

            Picture::where('uploaded_by_id', $tutorId)->delete();

            $shelterId = $tutor->shelter_id;
            $userId = $tutor->user_id;

            $tutor->delete();

            if ($shelterId) {
                Shelter::whereKey($shelterId)->delete();
            } elseif ($userId) {
                User::whereKey($userId)->delete();
            }

            $deletion->update([
                'completed_at' => now(),
                'deletion_status_id' => 4,
            ]);
        });
    }
}