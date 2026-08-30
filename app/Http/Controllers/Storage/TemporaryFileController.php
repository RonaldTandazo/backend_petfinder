<?php

namespace App\Http\Controllers\Storage;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storage\UploadTemporaryFileRequest;
use App\Services\Storage\TemporaryFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TemporaryFileController extends Controller
{
    public function __construct(protected TemporaryFileService $temporaryFileService) {}

    public function store(UploadTemporaryFileRequest $request): JsonResponse
    {
        try {
            $files = $request->file('files');
            $uuids  = $request->validated('uuids');

            $results = [];

            foreach ($files as $index => $file) {
                $uuid = $uuids[$index];
                $results[] = $this->temporaryFileService->upload($file, $uuid);
            }
            
            return $this->sendResponse(
                data    : ['files' => $results],
                message : 'Archivos subidos exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error subiendo archivo temporal: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo subir el archivo',
                error   : $th->getMessage()
            );
        }
    }

    public function show(string $key): JsonResponse
    {
        try {
            return $this->sendResponse(
                data    : ['url' => $this->temporaryFileService->previewUrl($key)],
                message : 'URL de previsualización generada'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error generando previsualización de archivo temporal: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo generar la previsualización',
                error   : $th->getMessage()
            );
        }
    }

    public function destroy(string $key): JsonResponse
    {
        try {
            $this->temporaryFileService->delete($key);

            return $this->sendResponse(
                message: 'Archivo eliminado exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando archivo temporal: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar el archivo',
                error   : $th->getMessage()
            );
        }
    }
}
