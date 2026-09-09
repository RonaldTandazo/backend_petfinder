<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterShelterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:100'],
            'business_name'      => ['nullable', 'string', 'max:100'],
            'tax_identification' => ['nullable', 'string', 'max:50'],
            'email'              => ['required', 'string', 'email', 'max:550', 'unique:shelters,email'],
            'password'           => ['required', 'string', Password::defaults()],
            'phone_mobile'       => ['nullable', 'string', 'max:15', 'regex:/^\+?[0-9]* ?[0-9]*$/'],
            'address'            => ['nullable', 'string', 'max:100'],
            'country_id'         => ['nullable', 'integer', 'exists:countries,id'],
            'city'               => ['nullable', 'string', 'max:25'],
            'latitude'           => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'longitude'          => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'web_page'           => ['nullable', 'string', 'url', 'max:150'],
            'business_hours'     => ['nullable', 'string', 'max:100'],
            'avatar'             => ['nullable', 'array', 'max:1',],
            'avatar.*.path_temp' => ['required', 'string', 'max:255'],
            'avatar.*.is_main'   => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'El nombre del refugio es obligatorio',
            'name.max'                    => 'El nombre del refugio no puede superar los 100 caracteres',
            'business_name.max'           => 'La razón social no puede superar los 100 caracteres',
            'tax_identification.max'      => 'La identificación tributaria no puede superar los 50 caracteres',
            'email.required'              => 'El correo electrónico es obligatorio',
            'email.email'                 => 'El correo electrónico debe tener un formato válido',
            'email.max'                   => 'El correo electrónico no puede superar los 50 caracteres',
            'email.unique'                => 'El correo electrónico ya se encuentra registrado',
            'password.required'           => 'La contraseña es obligatoria',
            'phone_mobile.max'            => 'El teléfono celular no debe superar los 15 caracteres',
            'phone_mobile.regex'          => 'El teléfono celular debe tener un formato válido (ej. +593 962618451 o 0962618451)',
            'address.max'                 => 'La dirección no puede superar los 100 caracteres',
            'country_id.exists'           => 'El país seleccionado no es válido',
            'city.max'                    => 'La ciudad no puede superar los 25 caracteres',
            'latitude.regex'              => 'La latitud debe ser un número válido con máximo 8 decimales',
            'longitude.regex'             => 'La longitud debe ser un número válido con máximo 8 decimales',
            'web_page.url'                => 'El formato de la página web debe ser una URL válida',
            'web_page.max'                => 'La página web no puede superar los 150 caracteres',
            'business_hours.max'          => 'El horario de atención no puede superar los 100 caracteres',
            'avatar.array'                => 'El formato del avatar es inválido',
            'avatar.max'                  => 'No puede adjuntar más de 1 foto',
            'avatar.*.path_temp.required' => 'El archivo temporal de la imagen es obligatorio',
            'avatar.*.path_temp.string'   => 'El archivo temporal de la imagen debe ser una cadena válida',
            'avatar.*.is_main.required'   => 'Debe indicar cuál foto es la principal',
            'avatar.*.is_main.boolean'    => 'El campo principal debe ser verdadero o falso',
        ];
    }
}