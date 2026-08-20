<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\RegisterShelterRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\LoginShelterRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\ShelterResource;
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

                $user->load(['country', 'gender', 'tutor']);

                return [
                    'user' => new UserResource($user)
                ];
            });

            return $this->sendResponse(
                data: $result,
                message: 'Usuario registrado exitosamente',
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $th) {
            Log::error('Error registrando usuario: ' . $th->getMessage(), [
                'exception' => $th,
            ]);

            return $this->sendError(
                message: 'No se pudo completar el registro del usuario.',
                error: $th->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function registerShelter(RegisterShelterRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $shelterData = array_merge($request->validated(), [
                    'verified' => false,
                ]);

                $shelter = Shelter::create($shelterData);

                $shelterTutorType = TutorType::where('tag', 'SHELTER')->first();

                Tutor::create([
                    'tutor_type_id' => $shelterTutorType?->id ?? 2,
                    'user_id' => null,
                    'shelter_id' => $shelter->id,
                ]);

                $shelter->load(['country', 'tutor']);

                return [
                    'shelter' => new ShelterResource($shelter)
                ];
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
                message: 'No se pudo completar el registro del refugio.',
                error: $th->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function loginUser(LoginUserRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $user = User::where($field, $credentials['login'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return $this->sendError(
                    message: 'Las credenciales ingresadas son incorrectas.',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }

            $user->tokens()->delete();

            $token = $user->createToken('user_auth_token')->plainTextToken;

            $user->load(['country', 'gender', 'tutor']);

            return $this->sendResponse(
                data: [
                    'user'         => new UserResource($user),
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ],
                message: 'Inicio de sesión exitoso.'
            );
        } catch (Throwable $th) {
            Log::error('Error en loginUser: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'Error al intentar iniciar sesión.',
                error: $th->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function loginShelter(LoginShelterRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            $shelter = Shelter::where('email', $credentials['email'])->first();

            if (! $shelter || ! Hash::check($credentials['password'], $shelter->password)) {
                return $this->sendError(
                    message: 'Las credenciales ingresadas son incorrectas.',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }

            $shelter->tokens()->delete();

            $token = $shelter->createToken('shelter_auth_token')->plainTextToken;

            $shelter->load(['country', 'tutor']);

            return $this->sendResponse(
                data: [
                    'shelter'      => new ShelterResource($shelter),
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ],
                message: 'Inicio de sesión exitoso.'
            );
        } catch (Throwable $th) {
            Log::error('Error en loginShelter: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'Error al intentar iniciar sesión.',
                error: $th->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse(
                message: 'Sesión cerrada correctamente.'
            );
        } catch (Throwable $th) {
            Log::error('Error en logout: ' . $th->getMessage(), ['exception' => $th]);

            return $this->sendError(
                message: 'Error al cerrar sesión.',
                error: $th->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}