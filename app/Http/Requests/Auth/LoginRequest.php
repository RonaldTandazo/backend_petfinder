<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                      => ['required', 'string', 'email'],
            'password'                   => ['required', 'string'],
            'account_type'               => ['nullable', 'string', Rule::in(['user', 'shelter'])],
            'is_account_deletion_active' => ['nullable', 'boolean'],
            'cancel_account_deletion'    => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_account_deletion_active' => $this->boolean('is_account_deletion_active'),
            'cancel_account_deletion'    => $this->boolean('cancel_account_deletion')
        ]);
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'El correo electrónico es obligatorio',
            'email.email'       => 'Debe ingresar un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
            'account_type.in'   => 'El tipo de cuenta seleccionado no es válido',
        ];
    }
}