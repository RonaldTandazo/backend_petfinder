<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor;
    }

    public function rules(): array
    {
        return [
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
            'new_password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'          => 'La contraseña actual es obligatoria.',
            'new_password.required'              => 'La nueva contraseña es obligatoria.',
            'new_password.confirmed'             => 'Las contraseñas no coinciden.',
            'new_password.min'                   => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password_confirmation.required' => 'Debe confirmar la nueva contraseña.',
        ];
    }
}