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
            'current_password'           => ['required', 'string'],
            'new_password'               => ['required', 'string', Password::defaults(), 'confirmed'],
            'new_password_confirmation'  => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'           => 'La contraseña actual es obligatoria',
            'new_password.required'               => 'La nueva contraseña es obligatoria',
            'new_password.confirmed'              => 'La confirmación de la nueva contraseña no coincide',
            'new_password_confirmation.required'  => 'Debe confirmar la nueva contraseña',
        ];
    }
}