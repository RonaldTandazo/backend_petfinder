<?php

namespace App\Http\Controllers\Legal;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\LegalDocumentVersionRequest;
use App\Services\Legal\LegalDocumentService;
use App\Services\Legal\LegalDocumentVersionService;
use App\Services\Legal\LegalDocumentStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LegalDocumentVersionController extends Controller
{
    public function __construct(
        protected LegalDocumentService $legalDocumentService,
        protected LegalDocumentVersionService $legalDocumentVersionService,
        protected LegalDocumentStorageService $legalDocumentStorageService
    ) {}

     public function publish(LegalDocumentVersionRequest $request): JsonResponse
    {
        $uploadedPaths = [];

        try {
            $documents = $request->safe()['new_documents'];

            $documentsToPublish = [];

            foreach ($documents as $document) {

                $paths = $this->legalDocumentStorageService->upload(
                    tag: $document['tag'],
                    version: $document['version'],
                    markdownFile: $document['markdown_file'],
                    pdfFile: $document['pdf_file']
                );

                $uploadedPaths = array_merge(
                    $uploadedPaths,
                    array_values($paths)
                );

                $documentsToPublish[] = [
                    'tag' => $document['tag'],
                    'version' => $document['version'],
                    'paths' => $paths,
                ];
            }

            $this->legalDocumentVersionService->publishMany($documentsToPublish);

            $this->legalDocumentService->clearCache();

            return $this->sendResponse(
                message: 'Nuevos documentos legales publicados',
                code: Response::HTTP_CREATED
            );

        } catch (CustomValidationException $e) {
            $this->cleanUploadedFiles($uploadedPaths);

            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            $this->cleanUploadedFiles($uploadedPaths);

            Log::error('Error al publicar los nuevos documentos legales: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo publicar los nuevos documentos legales',
                error   : $th->getMessage()
            );
        }
    }

    private function cleanUploadedFiles(array $paths): void
    {
        try {
            $this->legalDocumentStorageService->delete($paths);
        } catch (Throwable $th) {
            Log::error(
                'Error al eliminar archivos legales después de un fallo',
                [
                    'paths' => $paths,
                    'exception' => $th,
                ]
            );
        }
    }
}
