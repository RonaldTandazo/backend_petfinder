<?php

namespace App\Http\Controllers\Webhook;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Services\Webhook\MinioWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MinioWebhookController extends Controller
{
    public function __construct(protected MinioWebhookService $minioWebhookService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $expectedToken = (string) config('services.minio.webhook_token');
            $receivedToken = (string) $request->bearerToken();

            if ($expectedToken === '' || !hash_equals($expectedToken, $receivedToken)) {
                Log::warning('Webhook de MinIO rechazado: token inválido o no configurado.');

                return $this->sendError(
                    message : 'No autorizado',
                    code    : Response::HTTP_UNAUTHORIZED
                );
            }

            $result = $this->minioWebhookService->handle($request->all());

            return $this->sendResponse(
                data    : $result,
                message : 'Evento de MinIO procesado'
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error procesando webhook de MinIO: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo procesar el evento de MinIO',
                error   : $th->getMessage()
            );
        }
    }
}
