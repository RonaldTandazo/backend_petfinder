<?php

namespace App\Http\Controllers\Help;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Help\FormSupportRequest;
use App\Services\Help\SupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SupportController extends Controller
{
    public function __construct(protected SupportService $supportService) {}

    public function store(FormSupportRequest $request): JsonResponse
    {
        try {
            $support = $this->supportService->create($request->safe(), $this->getTutorId());

            return $this->sendResponse(
                data    : ['support_id' => $support->id],
                message : 'Hemos registrado el Soporte',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando soporte: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo registrar el Soporte',
                error   : $th->getMessage()
            );
        }
    }
}
