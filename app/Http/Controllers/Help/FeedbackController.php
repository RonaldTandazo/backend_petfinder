<?php

namespace App\Http\Controllers\Help;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Help\FormFeedbackRequest;
use App\Services\Help\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FeedbackController extends Controller
{
    public function __construct(protected FeedbackService $feedbackService) {}

    public function store(FormFeedbackRequest $request): JsonResponse
    {
        try {
            $feedback = $this->feedbackService->create($request->safe(), $this->getTutorId());

            return $this->sendResponse(
                data    : ['feedback_id' => $feedback->id],
                message : 'Hemos registrado el Feedback',
                code    : Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error registrando feedback: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'No se pudo registrar el Feedback',
                error   : $th->getMessage()
            );
        }
    }
}
