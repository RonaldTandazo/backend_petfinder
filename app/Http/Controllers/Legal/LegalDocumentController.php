<?php

namespace App\Http\Controllers\Legal;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\LegalDocumentRequest;
use App\Services\Legal\LegalDocumentService;
use App\Services\Legal\LegalDocumentStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LegalDocumentController extends Controller
{
    public function __construct(
        protected LegalDocumentService $legalDocumentService,
        protected LegalDocumentStorageService $legalDocumentStorageService
    ) {}

     public function getDocument(LegalDocumentRequest $request): JsonResponse
    {
        try {
            $tag = $request->safe()['tag'];

            $legalDocument = $this->legalDocumentService->getCurrentVersion($tag);

            $markdownContent = $this->legalDocumentStorageService->readMarkdown($legalDocument['md_path']);

            $data = [
                'content' => $markdownContent
            ];

            return $this->sendResponse(
                data: $data,
                message: 'Documento Legal Obtenido',
                code: Response::HTTP_CREATED
            );

        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener documento legal: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener documento legal',
                error   : $th->getMessage(),
            );
        }
    }

    public function downloadPdf(LegalDocumentRequest $request): JsonResponse|StreamedResponse
    {
        try {
            $tag = $request->safe()['tag'];

            $legalDocument = $this->legalDocumentService->getCurrentVersion($tag);

            if (empty($legalDocument['pdf_path'])) {
                throw new CustomValidationException(
                    'Esta versión del documento no tiene un PDF disponible'
                );
            }

            $filename = "{$tag}-v{$legalDocument['version']}.pdf";

            return $this->legalDocumentStorageService->downloadPdf(
                $legalDocument['pdf_path'],
                $filename
            );

        } catch (CustomValidationException $e) {
            return $this->sendError(
                message: $e->getMessage(),
                error: $e->errors(),
                code: $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al descargar documento legal: ' . $th->getMessage(), [
                'exception' => $th
            ]);

            return $this->sendError(
                message: 'No se pudo descargar el documento legal',
                error: $th->getMessage()
            );
        }
    }
}
