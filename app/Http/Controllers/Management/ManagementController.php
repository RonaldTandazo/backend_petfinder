<?php

namespace App\Http\Controllers\Management;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Services\Management\ManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ManagementController extends Controller
{
    public function __construct(protected ManagementService $managementService) {}

    public function deleteAccount(): JsonResponse
    {
        try {
            $tutorId = $this->getTutorId();

            $existsDeletion = $this->managementService->existsPendingDeletion($tutorId);
            if($existsDeletion){
                throw new CustomValidationException(
                    'Ya existe una solicitud de eliminación pendiente'
                );
            }

            $deletion = $this->managementService->deleteAccount($tutorId);

            return $this->sendResponse(
                message: 'Tu cuenta tiene programada su eliminación para el ' . $deletion->schedule_deletion_at->format('d/m/Y'),
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
