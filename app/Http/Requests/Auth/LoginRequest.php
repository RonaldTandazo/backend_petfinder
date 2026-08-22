<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'        => ['required', 'string', 'email'],
            'password'     => ['required', 'string'],
            'account_type' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'El correo electrónico es obligatorio',
            'email.email'       => 'Debe ingresar un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
        ];
    }
}