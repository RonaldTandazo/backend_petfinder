<?php

namespace App\Http\Controllers\Community;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Community\StorePostRequest;
use App\Http\Requests\Community\UpdatePostRequest;
use App\Services\Community\CommunityAuthorService;
use App\Services\Community\PostService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected CommunityAuthorService $authorService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $page  = $request->integer('page', 1);
            $limit = $request->integer('limit', 20);

            $result = $this->postService->list($page, $limit, $this->getTutorId());

            return $this->sendResponse(
                data    : $result,
                message : 'Listado de publicaciones obtenido exitosamente'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error al obtener el listado de publicaciones: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el listado de publicaciones',
                error   : $th->getMessage()
            );
        }
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $post = $this->postService->publish(
                $request->validated(),
                $this->authorService->build($user)
            );

            return $this->sendResponse(
                data    : ['post_id' => (string) $post->id],
                message : 'Publicación creada exitosamente',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error creando publicación: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo crear la publicación',
                error   : $th->getMessage()
            );
        }
    }

    public function show(string $postId): JsonResponse
    {
        try {
            $post = $this->postService->show($postId, $this->getTutorId());

            return $this->sendResponse(
                data    : $post,
                message : 'Detalle de la publicación obtenido exitosamente'
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
            Log::error('Error al obtener el detalle de la publicación: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo obtener el detalle de la publicación',
                error   : $th->getMessage()
            );
        }
    }

    public function update(UpdatePostRequest $request, string $postId): JsonResponse
    {
        try {
            $post = $this->postService->update($postId, $this->getTutorId(), $request->validated());

            return $this->sendResponse(
                data    : ['post_id' => (string) $post->id],
                message : 'Publicación actualizada exitosamente'
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
            Log::error('Error actualizando publicación: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo actualizar la publicación',
                error   : $th->getMessage()
            );
        }
    }

    public function destroy(string $postId): JsonResponse
    {
        try {
            $this->postService->delete($postId, $this->getTutorId());

            return $this->sendResponse(
                message: 'Publicación eliminada exitosamente'
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
            Log::error('Error eliminando publicación: '.$th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo eliminar la publicación',
                error   : $th->getMessage()
            );
        }
    }
}
