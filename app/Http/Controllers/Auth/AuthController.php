<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Auth\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\RegisterShelterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tutor;
use App\Models\TutorType;
use App\Models\User;
use App\Models\Shelter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        protected AuthHelper $authHelper
    ) {}

    public function registerUser(RegisterUserRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $user = User::create($request->validated());

                $userTutorType = TutorType::where('tag', 'USER')->first();

                Tutor::create([
                    'tutor_type_id' => $userTutorType?->id ?? 1,
                    'user_id' => $user->id,
                    'shelter_id' => null,
                ]);
            });

            return $this->sendResponse(
                message: 'Usuario registrado exitosamente',
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $th) {
            Log::error('Error registrando usuario: ' . $th->getMessage(), [
                'exception' => $th,
            ]);

            return $this->sendError(
                message: 'No se pudo completar el registro del usuario',
                error: $th->getMessage()
            );
        }
    }

    public function registerShelter(RegisterShelterRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $shelterData = array_merge(
                    $request->validated(),
                    [
                        'verified' => false,
                    ]
                );

                $shelter = Shelter::create($shelterData);

                $shelterTutorType = TutorType::where('tag', 'SHELTER')->first();

                Tutor::create([
                    'tutor_type_id' => $shelterTutorType?->id ?? 2,
                    'user_id' => null,
                    'shelter_id' => $shelter->id,
                ]);
            });

            return $this->sendResponse(
                data: $result,
                message: 'Refugio registrado exitosamente',
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $th) {
            Log::error('Error registrando refugio: ' . $th->getMessage(), [
                'exception' => $th,
            ]);

            return $this->sendError(
                message: 'No se pudo completar el registro del refugio',
                error: $th->getMessage()
            );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            $email = $credentials['email'];
            $password = $credentials['password'];
            $selectedType = $credentials['account_type'] ?? null;

            $accounts = $this->authHelper->findAccountsByEmail($email);
            $user = $accounts['user'];
            $shelter = $accounts['shelter'];

            if (!$user && !$shelter) {
                return $this->sendError(
                    message: 'Las credenciales ingresadas son incorrectas',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }

            if ($selectedType) {
                $targetAccount = $selectedType === 'user' ? $user : $shelter;

                if (!$targetAccount || !Hash::check($password, $targetAccount->password)) {
                    return $this->sendError(
                        message: 'Las credenciales ingresadas son incorrectas',
                        code: Response::HTTP_UNAUTHORIZED
                    );
                }

                return $this->sendResponse(
                    data: $this->authHelper->generateAuthPayload($targetAccount, $selectedType),
                    message: 'Inicio de sesión exitoso'
                );
            }

            $userValid = $user && Hash::check($password, $user->password);
            $shelterValid = $shelter && Hash::check($password, $shelter->password);

            if (!$userValid && !$shelterValid) {
                return $this->sendError(
                    message: 'Las credenciales ingresadas son incorrectas',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }

            if ($userValid && $shelterValid) {
                return $this->sendResponse(
                    data: [
                        'requires_account_selection' => true,
                        'available_accounts' => ['user', 'shelter'],
                    ],
                    message: 'Seleccione con qué tipo de cuenta desea ingresar'
                );
            }

            $activeAccount = $userValid ? $user : $shelter;
            $type = $userValid ? 'user' : 'shelter';

            return $this->sendResponse(
                data: $this->authHelper->generateAuthPayload($activeAccount, $type),
                message: 'Inicio de sesión exitoso'
            );
        } catch (Throwable $th) {
            Log::error('Error en login: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'Error al intentar iniciar sesión',
                error: $th->getMessage()
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse(
                message: 'Sesión cerrada correctamente'
            );
        } catch (Throwable $th) {
            Log::error('Error en logout: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'Error al cerrar sesión',
                error: $th->getMessage()
            );
        }
    }
}