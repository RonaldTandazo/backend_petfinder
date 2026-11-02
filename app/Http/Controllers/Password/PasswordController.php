<?php

namespace App\Http\Controllers\Password;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Http\Requests\Password\ShowPasswordRequest;
use App\Mail\Password\ForgotPasswordMail;
use App\Services\Mail\MailService;
use App\Services\Password\PasswordService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PasswordController extends Controller
{
    public function __construct(
        protected MailService $mailService,
        protected PasswordService $passwordService,
    ) {}

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordService->forgotPassword($request->safe());

            return match ($result['status']) {
                'not_found' => $this->sendError(
                    message: 'El correo electrónico no está registrado',
                    code: Response::HTTP_NOT_FOUND
                ),
                'select_account' => $this->sendResponse(
                    data: $result['payload'],
                    message: 'Selecciona qué tipo de cuenta deseas recuperar'
                ),
                'found' => $this->handlePasswordReset($result['payload']),
            };
        } catch (CustomValidationException $e) {
            return $this->sendError(
                message : $e->getMessage(),
                error   : $e->errors(),
                code    : $e->getCode()
            );
        } catch (Throwable $th) {
            Log::error('Error en forgotPassword: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message : 'Error al procesar la solicitud',
                error   : $th->getMessage()
            );
        }
    }

    private function handlePasswordReset(array $payload): JsonResponse
    {
        $account = $payload['account'];
        $accountType = $payload['account_type'];

        try {
            $reset = $this->passwordService->generatePasswordResetToken($account->email, $accountType);

            $this->mailService->queue(
                // $account->email,
                'edu09085@hotmail.com',
                new ForgotPasswordMail(
                    $account->name,
                    $reset['url']
                )
            );
        } catch (Throwable $th) {
            Log::error('Error al procesar recuperación de contraseña', [
                'account_type' => $accountType,
                'exception' => $th,
            ]);
        }

        return $this->sendResponse(
            data: null,
            message: 'Si existe una cuenta asociada a este correo, recibirás instrucciones para restablecer tu contraseña'
        );
    }

    public function show(ShowPasswordRequest $request, string $token): View
    {
        $isValid = $this->passwordService->validatePasswordResetToken(
            email: $request->safe()->email,
            accountType: $request->safe()->account_type,
            token: $token
        );

        if (!$isValid) {
            return view('web.password.password-reset-expired');
        }

        return view('web.password.password-reset', [
            'token' => $token,
            'email' => $request->safe()->email,
            'accountType' => $request->safe()->account_type,
        ]);
    }

    public function reset(ResetPasswordRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();

        $success = $this->passwordService->resetPassword(
            email: $validated['email'],
            accountType: $validated['account_type'],
            token: $validated['token'],
            password: $validated['password'],
        );

        if (!$success) {
            return back()
                ->withErrors([
                    'token' => 'El enlace de recuperación no es válido o ha expirado. Solicita uno nuevo.',
                ])
                ->withInput();
        }

        return view('web.password.password-reset-success');
    }
}
