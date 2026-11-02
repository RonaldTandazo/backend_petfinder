<?php

namespace App\Http\Controllers\Help;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Help\FormSupportRequest;
use App\Http\Resources\Help\HelpResource;
use App\Mail\Help\HelpMail;
use App\Services\Help\SupportService;
use App\Services\Mail\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SupportController extends Controller
{
    public function __construct(
        protected SupportService $supportService,
        protected MailService $mailService
    ) {}

    public function store(FormSupportRequest $request): JsonResponse
    {
        try {
            $support = $this->supportService->create($request->safe(), $this->getTutorId());

            try {
                $this->mailService->queue(
                    config('services.help.email'),
                    new HelpMail(
                        (new HelpResource($support))->resolve(),
                        'support'
                    )
                );
            } catch (Throwable $th) {
                Log::error('Error encolando correo de soporte', [
                    'support_id' => $support->id,
                    'exception' => $th,
                ]);
            }

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
