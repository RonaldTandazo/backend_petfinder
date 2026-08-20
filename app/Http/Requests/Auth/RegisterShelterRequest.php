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
            'email'              => ['required', 'string', 'email', 'max:150', 'unique:shelters,email'],
            'password'           => ['required', 'string', Password::defaults()],
            'telephone'          => ['nullable', 'string', 'max:20'],
            'physical_address'   => ['nullable', 'string', 'max:255'],
            'country_id'         => ['nullable', 'integer', 'exists:countries,id'],
            'city'               => ['nullable', 'string', 'max:100'],
            'latitude'           => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['nullable', 'numeric', 'between:-180,180'],
            'web_page'           => ['nullable', 'string', 'url', 'max:150'],
            'business_hours'     => ['nullable', 'string', 'max:100'],
            'logo'               => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'El correo electrónico ya se encuentra registrado',
            'country_id.exists'  => 'El país seleccionado no es válido',
            'web_page.url'       => 'El formato de la página web debe ser una URL válida',
            'latitude.between'   => 'La latitud debe estar dentro de un rango geográfico válido',
            'longitude.between'  => 'La longitud debe estar dentro de un rango geográfico válido',
        ];
    }
}