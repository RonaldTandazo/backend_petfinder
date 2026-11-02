<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'        => ['required', 'string'],
            'email'        => ['required', 'string', 'email'],
            'account_type' => ['required', 'string', Rule::in(['user', 'shelter'])],
            'password'     => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'        => 'El token es obligatorio',
            'email.required'        => 'El correo electrónico es obligatorio',
            'email.email'           => 'Debe ingresar un correo electrónico válido',
            'account_type.required' => 'El tipo de cuenta es obligatorio',
            'account_type.in'       => 'El tipo de cuenta seleccionado no es válido',
            'password.required'     => 'La nueva contraseña es obligatoria',
            'password.confirmed'    => 'Las contraseñas no coinciden',
            'password.letters'      => 'La contraseña debe contener al menos una letra',
            'password.numbers'      => 'La contraseña debe contener al menos un número',
        ];
    }
}