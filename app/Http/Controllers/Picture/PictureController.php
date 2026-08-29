<?php

namespace App\Http\Controllers\Picture;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Picture\UpdatePicturesRequest;
use App\Services\Picture\PictureService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PictureController extends Controller
{
    public function __construct(protected PictureService $pictureService) {}

    public function update(UpdatePicturesRequest $request): JsonResponse
    {
        try {
            $this->pictureService->updateMany(
                $request->validated()['pictures'],
                $this->getTutorId(),
                $this->getMainId()
            );

            return $this->sendResponse(
                message: 'Fotos actualizadas exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Una o más fotos no existen o no te pertenecen',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando fotos: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar las fotos',
                error   : $th->getMessage()
            );
        }
    }

    public function file(Request $request, int $picture): JsonResponse
    {
        try {
            $url = $this->pictureService->resolveUrl($picture, $request->boolean('temp'), $this->getTutorId());

            return $this->sendResponse(
                data    : ['url' => $url],
                message : 'URL del archivo obtenida'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Foto no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error obteniendo archivo de foto: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el archivo',
                error   : $th->getMessage()
            );
        }
    }

    public function destroy(int $picture): JsonResponse
    {
        try {
            $this->pictureService->delete($picture, $this->getTutorId());

            return $this->sendResponse(
                message: 'Foto eliminada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Foto no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando foto: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar la foto',
                error   : $th->getMessage()
            );
        }
    }
}
