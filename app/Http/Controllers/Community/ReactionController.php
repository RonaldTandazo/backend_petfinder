<?php

namespace App\Http\Controllers\Community;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Services\Community\CommunityAuthorService;
use App\Services\Community\ReactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReactionController extends Controller
{
    public function __construct(
        protected ReactionService $reactionService,
        protected CommunityAuthorService $authorService
    ) {}

    public function store(Request $request, string $postId): JsonResponse
    {
        try {
            $result = $this->reactionService->toggle(
                $postId,
                $this->authorService->build($request->user())
            );

            return $this->sendResponse(
                data    : $result,
                message : 'Reacción actualizada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Publicación no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando reacción: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar la reacción',
                error   : $th->getMessage()
            );
        }
    }

    public function destroy(Request $request, string $postId): JsonResponse
    {
        try {
            $result = $this->reactionService->unreact(
                $postId,
                $this->authorService->build($request->user())
            );

            return $this->sendResponse(
                data    : $result,
                message : 'Reacción eliminada exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Publicación no encontrada',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando reacción: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar la reacción',
                error   : $th->getMessage()
            );
        }
    }
}
