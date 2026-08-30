<?php

namespace App\Http\Controllers\Community;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Community\StoreCommentRequest;
use App\Http\Requests\Community\UpdateCommentRequest;
use App\Services\Community\CommentService;
use App\Services\Community\CommunityAuthorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
        protected CommunityAuthorService $authorService
    ) {}

    public function index(Request $request, string $postId): JsonResponse
    {
        try {
            $page  = $request->integer('page', 1);
            $limit = $request->integer('limit', 20);

            $result = $this->commentService->list($postId, $page, $limit);

            return $this->sendResponse(
                data    : $result,
                message : 'Listado de comentarios obtenido exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener el listado de comentarios: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el listado de comentarios',
                error   : $th->getMessage()
            );
        }
    }

    public function store(StoreCommentRequest $request, string $postId): JsonResponse
    {
        try {
            $user = $request->user();

            $comment = $this->commentService->store(
                $postId,
                $request->input('content'),
                $this->authorService->build($user)
            );

            return $this->sendResponse(
                data    : ['comment_id' => (string) $comment->id],
                message : 'Comentario creado exitosamente',
                code    : Response::HTTP_CREATED
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
            Log::error('Error creando comentario: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo crear el comentario',
                error   : $th->getMessage()
            );
        }
    }

    public function update(UpdateCommentRequest $request, string $commentId): JsonResponse
    {
        try {
            $comment = $this->commentService->update(
                $commentId,
                $this->getTutorId(),
                $request->input('content')
            );

            return $this->sendResponse(
                data    : ['comment_id' => (string) $comment->id],
                message : 'Comentario actualizado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Comentario no encontrado',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error actualizando comentario: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar el comentario',
                error   : $th->getMessage()
            );
        }
    }

    public function destroy(string $commentId): JsonResponse
    {
        try {
            $this->commentService->delete($commentId, $this->getTutorId());

            return $this->sendResponse(
                message : 'Comentario eliminado exitosamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendError(
                message : 'Comentario no encontrado',
                code    : Response::HTTP_NOT_FOUND
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error eliminando comentario: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar el comentario',
                error   : $th->getMessage()
            );
        }
    }
}
