<?php

namespace App\Http\Controllers\Management;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Services\Management\ManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ManagementController extends Controller
{
    public function __construct(protected ManagementService $managementService) {}

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->accountService->changePassword($request->user(), $request->safe());

            return $this->sendResponse(
                message : 'Contraseña actualizada exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando contraseña: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar la contraseña',
                error   : $th->getMessage()
            );
        }
    }

    public function deleteAccount(): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $existsDeletion = $this->managementService->existsAccountDeletion($tutorId);
            if($existsDeletion){
                throw new CustomValidationException(
                    'Ya existe una solicitud de eliminación pendiente'
                );
            }

            $deletion = $this->managementService->deleteAccount($tutorId);

            $deletionDate = ucfirst($deletion->schedule_deletion_at->locale('es')->translatedFormat('d-F-Y'));

            return $this->sendResponse(
                message: 'Tu cuenta tiene programada su eliminación el ' . $deletionDate,
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al registrar solicitud ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo registrar tu solicitud',
                error   : $th->getMessage(),
            );
        }
    }
}
