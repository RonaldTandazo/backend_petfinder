<?php

namespace App\Http\Controllers\Legal;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\LegalDocumentAcceptanceRequest;
use App\Services\Legal\LegalDocumentAcceptanceService;
use App\Services\Legal\LegalDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LegalDocumentAcceptanceController extends Controller
{
    public function __construct(
        protected LegalDocumentService $legalDocumentService,
        protected LegalDocumentAcceptanceService $legalDocumentAcceptanceService
    ) {}

    public function acceptance(LegalDocumentAcceptanceRequest $request): JsonResponse
    {
        try {
            $tags = $request->safe()['accepted_documents'];
            $legalDocuments = $this->legalDocumentService->getCurrentVersions();

            $this->legalDocumentAcceptanceService->acceptPendingDocuments($legalDocuments, $this->getTutorId(), $tags);

            return $this->sendResponse(
                message : 'Documentos Legales Aceptados',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al registrar aceptación de documentos legales: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo registrar aceptación de documentos legales',
                error   : $th->getMessage()
            );
        }
    }
}
