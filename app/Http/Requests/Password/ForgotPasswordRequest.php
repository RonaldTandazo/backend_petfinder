<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'        => ['required', 'string', 'email'],
            'account_type' => ['nullable', 'string', Rule::in(['user', 'shelter'])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'  => 'El correo electrónico es obligatorio',
            'email.email'     => 'Debe ingresar un correo electrónico válido',
            'account_type.in' => 'El tipo de cuenta seleccionado no es válido',
        ];
    }
}