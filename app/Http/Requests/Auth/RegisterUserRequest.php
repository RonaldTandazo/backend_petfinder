<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_names' => ['required', 'string', 'max:100'],
            'last_names'  => ['required', 'string', 'max:100'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'       => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password'    => ['required', 'string', Password::defaults()],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'country_id'  => ['nullable', 'exists:countries,id'],
            'gender_id'   => ['nullable', 'exists:genders,id'],
            'city'        => ['nullable', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:255'],
            'avatar'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'   => 'El nombre de usuario ya está registrado.',
            'email.unique'      => 'El correo electrónico ya se encuentra registrado.',
            'country_id.exists' => 'El país seleccionado no es válido.',
            'gender_id.exists'  => 'El género seleccionado no es válido.',
        ];
    }
}