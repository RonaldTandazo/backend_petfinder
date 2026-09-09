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
            'first_names'        => ['required', 'string', 'max:50'],
            'last_names'         => ['required', 'string', 'max:50'],
            'email'              => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'password'           => ['required', 'string', Password::defaults()],
            'phone_mobile'       => ['nullable', 'string', 'max:15', 'regex:/^\+?[0-9]* ?[0-9]*$/'],
            'country_id'         => ['nullable', 'integer', 'exists:countries,id'],
            'gender_id'          => ['nullable', 'integer', 'exists:genders,id'],
            'city'               => ['nullable', 'string', 'max:25'],
            'address'            => ['nullable', 'string', 'max:100'],
            'avatar'             => ['nullable', 'array', 'max:1',],
            'avatar.*.path_temp' => ['required', 'string', 'max:255'],
            'avatar.*.is_main'   => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_names.required'        => 'Los nombres son obligatorios',
            'first_names.max'             => 'Los nombres no pueden superar los 50 caracteres',
            'last_names.required'         => 'Los apellidos son obligatorios',
            'last_names.max'              => 'Los apellidos no pueden superar los 50 caracteres',
            'email.required'              => 'El correo electrónico es obligatorio',
            'email.email'                 => 'El correo electrónico debe tener un formato válido',
            'email.unique'                => 'El correo electrónico ya se encuentra registrado',
            'email.max'                   => 'El correo electrónico no puede superar los 50 caracteres',
            'city.max'                    => 'La ciudad no puede superar los 25 caracteres',
            'address.max'                 => 'La dirección no puede superar los 100 caracteres',
            'password.required'           => 'La contraseña es obligatoria',
            'phone_mobile.max'            => 'El teléfono celular no debe superar los 15 caracteres',
            'phone_mobile.regex'          => 'El teléfono celular debe tener un formato válido (ej. +593 962618451 o 0962618451)',
            'country_id.exists'           => 'El país seleccionado no es válido',
            'gender_id.exists'            => 'El género seleccionado no es válido',
            'avatar.array'                => 'El formato del avatar es inválido',
            'avatar.max'                  => 'No puede adjuntar más de 1 foto',
            'avatar.*.path_temp.required' => 'El archivo temporal de la imagen es obligatorio',
            'avatar.*.path_temp.string'   => 'El archivo temporal de la imagen debe ser una cadena válida',
            'avatar.*.is_main.required'   => 'Debe indicar cuál foto es la principal',
            'avatar.*.is_main.boolean'    => 'El campo principal debe ser verdadero o falso',
        ];
    }
}